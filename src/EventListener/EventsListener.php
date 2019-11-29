<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Registry;
use App\Entity\Landlord\Event;
use App\Event\EmployeeCreated;
use App\Event\EmployeeFined;
use App\Event\EmployeeFired;
use App\Event\EmployeeSalaryIssued;
use App\Event\ExpenseCreated;
use App\Event\ExpenseItemCreated;
use App\Event\IncomeAccrued;
use App\Event\OrderAppointmentMade;
use App\Event\OrderClosed;
use App\Event\OrderStatusChanged;
use App\Event\OrganizationCreated;
use App\Event\PartAccrued;
use App\Event\PartCreated;
use App\Event\PartDecreased;
use App\Event\PartDeReserved;
use App\Event\PartReserved;
use App\Event\PaymentCreated;
use App\Event\PersonCreated;
use App\Request\EntityTransformer;
use App\State;
use function get_class;
use function is_object;
use LogicException;
use Serializable;
use function serialize;
use function sprintf;
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
        return [
            EmployeeCreated::class => 'onEvent',
            EmployeeFined::class => 'onEvent',
            EmployeeFired::class => 'onEvent',
            EmployeeSalaryIssued::class => 'onEvent',
            ExpenseCreated::class => 'onEvent',
            ExpenseItemCreated::class => 'onEvent',
            IncomeAccrued::class => 'onEvent',
            OrderAppointmentMade::class => 'onEvent',
            OrderClosed::class => 'onEvent',
            OrderStatusChanged::class => 'onEvent',
            OrganizationCreated::class => 'onEvent',
            PartAccrued::class => 'onEvent',
            PartCreated::class => 'onEvent',
            PartDecreased::class => 'onEvent',
            PartDeReserved::class => 'onEvent',
            PartReserved::class => 'onEvent',
            PaymentCreated::class => 'onEvent',
            PersonCreated::class => 'onEvent',
        ];
    }

    public function onEvent(GenericEvent $event, string $name): void
    {
        $subject = $event->getSubject();

        $arguments = [];
        $arguments['subject'] = $this->transformer->transform($subject);

        foreach ($event->getArguments() as $key => $argument) {
            if ($this->registry->isEntity($argument)) {
                $arguments['arguments'][$key] = $this->transformer->transform($argument);
            } elseif ($argument instanceof Serializable) {
                $arguments['arguments'][$key] = serialize($argument);
            } elseif (is_object($argument)) {
                throw new LogicException(sprintf('Object "%s" unsupported to EventLog', get_class($argument)));
            } else {
                $arguments['arguments'][$key] = $argument;
            }
        }

        $user = $this->state->user();
        $tenant = $this->state->tenant();

        $em = $this->registry->manager(Event::class);
        $em->persist(new Event($name, $arguments, $user, $tenant));
        $em->flush();
    }
}
