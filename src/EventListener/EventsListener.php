<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Customer\Event\OrganizationCreated;
use App\Customer\Event\PersonCreated;
use App\Employee\Event\EmployeeCreated;
use App\Employee\Event\EmployeeFined;
use App\Employee\Event\EmployeeFired;
use App\Employee\Event\EmployeeSalaryIssued;
use App\Entity\Landlord\Event;
use App\Expense\Event\ExpenseCreated;
use App\Expense\Event\ExpenseItemCreated;
use App\Income\Event\IncomeAccrued;
use App\Order\Event\OrderAppointmentMade;
use App\Order\Event\OrderClosed;
use App\Order\Event\OrderStatusChanged;
use App\Part\Event\PartAccrued;
use App\Part\Event\PartCreated;
use App\Part\Event\PartDecreased;
use App\Part\Event\PartDeReserved;
use App\Part\Event\PartReserved;
use App\Payment\Event\PaymentCreated;
use App\Request\EntityTransformer;
use App\Shared\Doctrine\Registry;
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
    private Registry $registry;

    private EntityTransformer $transformer;

    private State $state;

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
