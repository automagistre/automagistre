<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Doctrine\Registry;
use App\Entity\Landlord\User;
use App\Request\EntityTransformer;
use App\State;
use function array_keys;
use function array_merge;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use function method_exists;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use Pagerfanta\Pagerfanta;
use stdClass;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function trim;
use function urldecode;
use function urlencode;

/**
 * @method User getUser()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class AbstractController extends EasyAdminController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            Registry::class,
            EntityTransformer::class,
            MoneyFormatter::class,
            State::class,
            DecimalMoneyFormatter::class,
            PhoneNumberUtil::class,
            EasyAdminRouter::class,
        ]);
    }

    protected function formatMoney(Money $money, bool $decimal = false): string
    {
        $formatter = $decimal
            ? $this->container->get(DecimalMoneyFormatter::class)
            : $this->container->get(MoneyFormatter::class);

        return $formatter->format($money);
    }

    protected function formatTelephone(?PhoneNumber $telephone, int $format = PhoneNumberFormat::INTERNATIONAL): string
    {
        if (null === $telephone) {
            return '';
        }

        return $this->container->get(PhoneNumberUtil::class)->format($telephone, $format);
    }

    /**
     * @param object|string $entity
     */
    protected function redirectToEasyPath(
        $entity,
        string $action,
        array $parameters = [],
        int $status = 302
    ): RedirectResponse {
        return $this->redirect($this->generateEasyPath($entity, $action, $parameters), $status);
    }

    /**
     * @param object|string $entity
     */
    protected function generateEasyPath($entity, string $action, array $parameters = []): string
    {
        return $this->container->get(EasyAdminRouter::class)->generate($entity, $action, $parameters);
    }

    protected function setReferer(string $url): void
    {
        $this->request->query->set('referer', urlencode($url));
    }

    protected function redirectToReferrer(): RedirectResponse
    {
        $refererUrl = trim($this->request->query->get('referer', ''));

        return '' !== $refererUrl
            ? $this->redirect(
                urldecode($refererUrl)
            )
            : parent::redirectToReferrer();
    }

    /**
     * @template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return ?T
     */
    protected function getEntity(string $class): ?object
    {
        $entity = $this->container->get(EntityTransformer::class)->reverseTransform($class);

        if (null === $entity) {
            $entity = $this->request->attributes->get('easyadmin')['item'];
        }

        if (!$entity instanceof $class) {
            return null;
        }

        return $entity;
    }

    protected function initialize(Request $request): void
    {
        parent::initialize($request);

        if ('0' === $id = $request->query->get('id')) {
            $easyadmin = $request->attributes->get('easyadmin');
            $easyadmin['item'] = $this->container->get(Registry::class)->repository($easyadmin['entity']['class'])->find($id);

            $request->attributes->set('easyadmin', $easyadmin);
        }
    }

    protected function findCurrentEntity(): ?object
    {
        return $this->request->attributes->get('easyadmin')['item'] ?? null;
    }

    protected function event(GenericEvent $event): void
    {
        $this->container->get('event_dispatcher')->dispatch($event);
    }

    /**
     * {@inheritdoc}
     */
    protected function newAction(): Response
    {
        $this->dispatch(EasyAdminEvents::PRE_NEW);

        $entity = $this->createNewEntity();

        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $entity;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $fields = $this->entity['new']['fields'];

        $newForm = $this->createNewForm($entity, $fields);

        $newForm->handleRequest($this->request);
        if ($newForm->isSubmitted() && $newForm->isValid()) {
            $this->processUploadedFiles($newForm);

            $this->dispatch(EasyAdminEvents::PRE_PERSIST, ['entity' => $entity]);
            $entity = $this->persistEntity($entity) ?? $entity;
            $this->dispatch(EasyAdminEvents::POST_PERSIST, ['entity' => $entity]);

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_NEW, [
            'entity_fields' => $fields,
            'form' => $newForm,
            'entity' => $entity,
        ]);

        $parameters = [
            'form' => $newForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
        ];

        return $this->renderTemplate('new', $this->entity['templates']['new'], $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function isActionAllowed($actionName): bool
    {
        return parent::isActionAllowed($actionName);
    }

    /**
     * {@inheritdoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null
    ): QueryBuilder {
        return parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);
    }

    /**
     * {@inheritdoc}
     */
    protected function createNewEntity()
    {
        if (stdClass::class === ($this->entity['new']['form_options']['data_class'] ?? null)) {
            $entity = new stdClass();

            $entity->id = null;
            foreach (array_keys($this->entity['new']['fields']) as $field) {
                $entity->{$field} = null;
            }
        } else {
            $entity = parent::createNewEntity();
        }

        if (method_exists($entity, 'setCreatedBy')) {
            $entity->setCreatedBy($this->getUser());
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntityForm($entity, array $entityProperties, $view): FormInterface
    {
        return parent::createEntityForm($entity, $entityProperties, $view);
    }

    /**
     * {@inheritdoc}
     */
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
        return parent::createSearchQueryBuilder(
            $entityClass,
            $searchQuery,
            $searchableFields,
            $sortField,
            $sortDirection,
            $dqlFilter
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function findBy(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $page = 1,
        $maxPerPage = 15,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): Pagerfanta {
        return parent::findBy($entityClass, $searchQuery, $searchableFields, $page, $maxPerPage, $sortField, $sortDirection, $dqlFilter);
    }

    /**
     * {@inheritdoc}
     */
    protected function createDeleteForm($entityName, $entityId): FormInterface
    {
        return parent::createDeleteForm($entityName, $entityId);
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntityFormBuilder($entity, $view): FormBuilder
    {
        return parent::createEntityFormBuilder($entity, $view);
    }
}
