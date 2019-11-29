<?php

declare(strict_types=1);

namespace App\EasyAdmin\Notify;

use App\Entity\Tenant\OrderItem;
use App\Event\OrderStatusChanged;
use App\State;
use App\Tenant\EntityChecker;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Twig\EasyAdminTwigExtension;
use function get_class;
use function is_array;
use function json_encode;
use Sentry\Severity;
use Sentry\State\HubInterface;
use function sprintf;
use function str_replace;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Environment;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityNotificationListener implements EventSubscriberInterface
{
    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @var EasyAdminTwigExtension
     */
    private $easyAdminTwigExtension;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var HubInterface
     */
    private $sentry;

    /**
     * @var State
     */
    private $state;

    /**
     * @var EntityChecker
     */
    private $entityChecker;

    public function __construct(
        ConfigManager $configManager,
        EasyAdminTwigExtension $easyAdminTwigExtension,
        PropertyAccessorInterface $propertyAccessor,
        Environment $twig,
        PublisherInterface $publisher,
        HubInterface $sentry,
        State $state,
        EntityChecker $entityChecker
    ) {
        $this->configManager = $configManager;
        $this->easyAdminTwigExtension = $easyAdminTwigExtension;
        $this->propertyAccessor = $propertyAccessor;
        $this->twig = $twig;
        $this->publisher = $publisher;
        $this->sentry = $sentry;
        $this->state = $state;
        $this->entityChecker = $entityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EasyAdminEvents::POST_UPDATE => 'onCreateOrUpdate',
            EasyAdminEvents::POST_PERSIST => 'onCreateOrUpdate',
            OrderStatusChanged::class => 'onStatusChanged',
        ];
    }

    public function onCreateOrUpdate(GenericEvent $event, string $eventName): void
    {
        $entity = $event->getSubject();

        $this->notify($entity, EasyAdminEvents::POST_PERSIST === $eventName);

        if ($entity instanceof OrderItem) {
            $this->notify($entity->getOrder(), false);
        }
    }

    public function onStatusChanged(OrderStatusChanged $event): void
    {
        $this->notify($event->getSubject(), false);
    }

    private function notify(object $entity, bool $isNew): void
    {
        $class = str_replace('Proxies\\__CG__\\', '', get_class($entity));
        $entityConfig = $this->configManager->getEntityConfigByClass($class);
        if (!is_array($entityConfig)) {
            $this->sentry->captureMessage(
                sprintf('Not found config for entity class %s', $class), Severity::warning()
            );

            return;
        }

        $id = $this->propertyAccessor->getValue($entity, 'id');
        $name = $entityConfig['name'];

        $tenant = '';
        if ($this->entityChecker->isTenantEntity($entity)) {
            $tenant = $this->state->tenant()->getIdentifier().'/';
        }

        $topics = [
            "http://automagistre.ru/{$tenant}{$name}",
            "http://automagistre.ru/{$tenant}{$name}/{$id}",
        ];

        $data = [
            'id' => $id,
            'new' => $isNew,
            'tr' => $this->twig->render('easy_admin/default/list/table_body_item.html.twig', [
                'item' => $entity,
                '_entity_config' => $entityConfig,
                'fields' => $entityConfig['list']['fields'],
                '_trans_parameters' => [],
                '_list_item_actions' => $this->easyAdminTwigExtension->getActionsForItem('list', $name),
                '_request_parameters' => [],
            ]),
        ];

        ($this->publisher)(new Update($topics, json_encode($data, JSON_THROW_ON_ERROR)));
    }
}
