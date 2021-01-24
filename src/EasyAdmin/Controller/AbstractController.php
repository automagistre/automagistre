<?php

declare(strict_types=1);

namespace App\EasyAdmin\Controller;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Request\EntityTransformer;
use App\User\Entity\User;
use Closure;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Sentry\State\Scope;
use stdClass;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function array_keys;
use function array_merge;
use function assert;
use function in_array;
use function is_callable;
use function is_string;
use function mb_strtolower;
use function method_exists;
use function Sentry\configureScope;
use function sprintf;
use function Symfony\Component\String\u;
use function trim;
use function urldecode;
use function urlencode;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @property EntityManagerInterface $em
 *
 * @method User getUser()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class AbstractController extends EasyAdminController
{
    protected Registry $registry;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            Registry::class,
            EntityTransformer::class,
            MoneyFormatter::class,
            DecimalMoneyFormatter::class,
            PhoneNumberUtil::class,
            EasyAdminRouter::class,
            IdentifierFormatter::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function executeDynamicMethod($methodNamePattern, array $arguments = [])
    {
        if ([] === $arguments) {
            $arguments = [$this->request];
        }

        return parent::executeDynamicMethod($methodNamePattern, $arguments);
    }

    protected function display(Identifier $identifier, string $format = null): string
    {
        return $this->get(IdentifierFormatter::class)->format($identifier, $format);
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

    protected function redirectToEasyPath(
        string $entity,
        string $action,
        array $parameters = [],
        int $status = 302
    ): RedirectResponse {
        return $this->redirect($this->generateEasyPath($entity, $action, $parameters), $status);
    }

    protected function generateEasyPath(string $entity, string $action, array $parameters = []): string
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

    protected function getEntity(string $class, callable $callable = null): ?object
    {
        $entity = $this->container->get(EntityTransformer::class)->reverseTransform($class);

        if (null === $entity) {
            $entity = $this->request->attributes->get('easyadmin')['item'];
        }

        if (!$entity instanceof $class) {
            return null;
        }

        if (is_callable($callable)) {
            $callable($entity);
        }

        return $entity;
    }

    /**
     * @template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return ?T
     */
    protected function getIdentifier(string $class)
    {
        $uuid = $this->request->query->get(u($class)->afterLast('\\')->snake()->toString());

        if (is_string($uuid)) {
            $identifier = Identifier::fromClass($class, $uuid);

            assert($identifier instanceof $class);

            return $identifier;
        }

        return null;
    }

    protected function initialize(Request $request): void
    {
        parent::initialize($request);

        configureScope(function (Scope $scope) use ($request): void {
            $scope->setTags([
                'easyadmin.entity' => $this->entity['name'],
                'easyadmin.action' => $request->query->getAlpha('action'),
            ]);
        });

        $this->registry = $this->container->get(Registry::class);
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

    protected function createEditDto(Closure $callable): ?object
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function searchAction(): Response
    {
        $uuid = $this->request->query->get('query');

        if (
            is_string($uuid)
            && !in_array('show', $this->entity['disabled_actions'], true)
            && Uuid::isValid($uuid = trim($uuid))
        ) {
            return $this->redirectToEasyPath($this->entity['name'], 'show', ['id' => $uuid]);
        }

        return parent::searchAction();
    }

    /**
     * {@inheritdoc}
     */
    protected function editAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');

        /** @phpstan-ignore-next-line */
        $dtoClosure = fn (): array => $this->registry->repository($this->entity['class'])
            ->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', $this->request->get('id'))
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY)
        ;

        $entity = $this->createEditDto($dtoClosure) ?? $easyadmin['item'];

        $property = $this->request->query->get('property');

        if (null !== $property && $this->request->isXmlHttpRequest()) {
            $newValue = 'true' === mb_strtolower($this->request->query->get('newValue'));
            $fieldsMetadata = $this->entity['list']['fields'];

            if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
                throw new RuntimeException(sprintf('The type of the "%s" property is not "toggle".', $property));
            }

            $this->updateEntityProperty($entity, $property, $newValue);

            return new Response((string) ((int) $newValue));
        }

        $fields = $this->entity['edit']['fields'];

        $editForm = $this->executeDynamicMethod('create<EntityName>EditForm', [$entity, $fields]);
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->processUploadedFiles($editForm);

            $this->dispatch(EasyAdminEvents::PRE_UPDATE, ['entity' => $entity]);

            $entity = $this->executeDynamicMethod('update<EntityName>Entity', [$entity, $editForm]) ?? $entity;
            $this->dispatch(EasyAdminEvents::POST_UPDATE, ['entity' => $entity]);

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);

        $parameters = [
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ];

        return $this->executeDynamicMethod('render<EntityName>Template', [
            'edit',
            $this->entity['templates']['edit'],
            $parameters,
        ]);
    }
}
