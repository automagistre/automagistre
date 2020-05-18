<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Costil;
use App\Request\EntityTransformer;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\State;
use App\User\Entity\User;
use function array_keys;
use function array_merge;
use function assert;
use Closure;
use Doctrine\ORM\AbstractQuery;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use function is_callable;
use function is_string;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use function mb_strtolower;
use function method_exists;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use ReflectionClass;
use RuntimeException;
use function sprintf;
use stdClass;
use Symfony\Component\EventDispatcher\GenericEvent;
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
            State::class,
            DecimalMoneyFormatter::class,
            PhoneNumberUtil::class,
            EasyAdminRouter::class,
            IdentifierFormatter::class,
        ]);
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
     * @psalm-param class-string<Identifier> $class
     */
    protected function getIdentifier(string $class): ?Identifier
    {
        $uuid = $this->request->query->get(Costil::QUERY[$class]);
        if (is_string($uuid)) {
            /** @var callable $callable */
            $callable = $class.'::fromString';
            $identifier = $callable($uuid);
            assert($identifier instanceof Identifier);

            return $identifier;
        }

        return null;
    }

    protected function initialize(Request $request): void
    {
        parent::initialize($request);

        $this->registry = $this->container->get(Registry::class);

        if ('0' === $id = $request->query->get('id')) {
            $easyadmin = $request->attributes->get('easyadmin');
            /** @phpstan-ignore-next-line */
            $easyadmin['item'] = $this->registry->repository($easyadmin['entity']['class'])->find($id);

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
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        $entity = $this->createEditDto($dtoClosure) ?? $easyadmin['item'];

        if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
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

    /**
     * @template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     */
    protected function createWithoutConstructor(string $class)
    {
        return (new ReflectionClass($class))->newInstanceWithoutConstructor();
    }
}
