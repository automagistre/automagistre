<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Landlord\Event;
use App\Events;
use App\Request\EntityTransformer;
use App\Request\State;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EventsListener implements EventSubscriberInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var EntityTransformer
     */
    private $transformer;

    /**
     * @var State
     */
    private $state;

    public function __construct(RegistryInterface $registry, EntityTransformer $transformer, State $state)
    {
        $this->registry = $registry;
        $this->transformer = $transformer;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        $reflection = new \ReflectionClass(Events::class);

        return \array_map(function () {
            return 'onEvent';
        }, \array_flip(\array_values($reflection->getConstants())));
    }

    public function onEvent(GenericEvent $event, string $name): void
    {
        $subject = $event->getSubject();

        $arguments = [];
        $arguments['subject'] = $this->transformer->transform($subject);

        foreach ($event->getArguments() as $key => $argument) {
            if (\is_object($argument)) {
                $arguments['arguments'][$key] = $this->transformer->transform($argument);
            } else {
                $arguments['arguments'][$key] = $argument;
            }
        }

        $user = $this->state->user();
        $tenant = $this->isTenantEntity($subject) ? $this->state->tenant() : null;

        $em = $this->registry->getEntityManagerForClass(Event::class);
        $em->persist(new Event($name, $arguments, $user, $tenant));
        $em->flush();
    }

    private function isTenantEntity(object $entity): bool
    {
        return 'tenant' === $this->registry->getEntityManagerForClass(\get_class($entity))
                ->getConnection()
                ->getDatabase();
    }
}
