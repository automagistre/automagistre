<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Doctrine\Registry;
use App\Entity\Landlord\User;
use App\Request\EntityTransformer;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method User getUser()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class AbstractController extends AdminController
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var EntityTransformer
     */
    private $entityTransformer;

    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    /**
     * @var DecimalMoneyFormatter
     */
    private $decimalMoneyFormatter;

    /**
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @required
     */
    public function setEntityTransformer(EntityTransformer $entityTransformer): void
    {
        $this->entityTransformer = $entityTransformer;
    }

    /**
     * @required
     */
    public function setMoneyFormatter(MoneyFormatter $moneyFormatter): void
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * @required
     */
    public function setDecimalMoneyFormatter(DecimalMoneyFormatter $moneyFormatter): void
    {
        $this->decimalMoneyFormatter = $moneyFormatter;
    }

    /**
     * @required
     */
    public function setPhoneNumberUtil(PhoneNumberUtil $phoneNumberUtil): void
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    /**
     * @required
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @required
     */
    public function setRegistry(Registry $registry): void
    {
        $this->registry = $registry;
    }

    protected function formatMoney(Money $money, bool $decimal = false): string
    {
        $formatter = $decimal ? $this->decimalMoneyFormatter : $this->moneyFormatter;

        return $formatter->format($money);
    }

    protected function formatTelephone(?PhoneNumber $telephone, int $format = PhoneNumberFormat::INTERNATIONAL): string
    {
        if (null === $telephone) {
            return '';
        }

        return $this->phoneNumberUtil->format($telephone, $format);
    }

    protected function redirectToEasyPath(
        object $entity,
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
        return $this->container->get('easyadmin.router')->generate($entity, $action, $parameters);
    }

    protected function setReferer(string $url): void
    {
        $this->request->query->set('referer', \urlencode($url));
    }

    protected function redirectToReferrer(): RedirectResponse
    {
        $refererUrl = \trim($this->request->query->get('referer', ''));

        return '' !== $refererUrl
            ? $this->redirect(
                \urldecode($refererUrl)
            )
            : parent::redirectToReferrer();
    }

    protected function getEntity(string $class): ?object
    {
        $entity = $this->entityTransformer->reverseTransform($class);

        if (null === $entity) {
            $entity = $this->request->attributes->get('easyadmin')['item'];
        }

        if (!$entity instanceof $class) {
            return null;
        }

        return $entity;
    }

    protected function findCurrentEntity(): ?object
    {
        return $this->request->attributes->get('easyadmin')['item'] ?? null;
    }

    protected function event(string $eventName, object $event): void
    {
        if (!$event instanceof Event) {
            $event = new GenericEvent($event);
        }

        $this->dispatcher->dispatch($eventName, $event);
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
        $entity = parent::createNewEntity();

        if (\method_exists($entity, 'setCreatedBy')) {
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
    protected function createEntityFormBuilder($entity, $view): FormBuilderInterface
    {
        return parent::createEntityFormBuilder($entity, $view);
    }
}
