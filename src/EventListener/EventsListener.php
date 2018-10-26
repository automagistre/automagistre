<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Event;
use App\Events;
use App\Request\EntityTransformer;
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

    public function __construct(RegistryInterface $registry, EntityTransformer $transformer)
    {
        $this->registry = $registry;
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        $reflection = new \ReflectionClass(Events::class);

        $events = \array_values($reflection->getConstants());

        $events = \array_combine($events, \array_map(function () {
            return 'onEvent';
        }, $events));

        if (false === $events) {
            throw new \LogicException('Unexpected behaviour.');
        }
    }

    public function onEvent(GenericEvent $event, string $name): void
    {
        $em = $this->registry->getEntityManager();

        $arguments = [];
        if (null !== $subject = $event->getSubject()) {
            $arguments['subject'] = $this->transformer->transform($subject);
        }

        foreach ($event->getArguments() as $key => $argument) {
            if (\is_object($argument)) {
                $arguments['arguments'][$key] = $this->transformer->transform($argument);
            } else {
                $arguments['arguments'][$key] = $argument;
            }
        }

        $em->persist(new Event($name, $arguments));
        $em->flush();
    }
}
