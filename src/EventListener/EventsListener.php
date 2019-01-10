<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Registry;
use App\Entity\Landlord\Event;
use App\Events;
use App\Request\EntityTransformer;
use App\Request\State;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EventsListener implements EventSubscriberInterface
{
    /**
     * @var Registry
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

    public function __construct(Registry $registry, EntityTransformer $transformer, State $state)
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
            if ($this->registry->isEntity($argument)) {
                $arguments['arguments'][$key] = $this->transformer->transform($argument);
            } elseif ($argument instanceof \Serializable) {
                $arguments['arguments'][$key] = \serialize($argument);
            } elseif (\is_object($argument)) {
                throw new LogicException(\sprintf('Object "%s" unsupported to EventLog', \get_class($argument)));
            } else {
                $arguments['arguments'][$key] = $argument;
            }
        }

        $user = $this->state->user();
        $tenant = $this->registry->isTenantEntity($subject) ? $this->state->tenant() : null;

        $em = $this->registry->manager(Event::class);
        $em->persist(new Event($name, $arguments, $user, $tenant));
        $em->flush();
    }
}
