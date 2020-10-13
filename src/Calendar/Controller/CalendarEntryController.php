<?php

namespace App\Calendar\Controller;

use App\Calendar\Action\ChangeOrderCalendarEntryCommand;
use App\Calendar\Action\CreateCalendarEntryCommand;
use App\Calendar\Action\DeleteCalendarEntryCommand;
use App\Calendar\Action\RescheduleCalendarEntryCommand;
use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\EntryOrder;
use App\Calendar\Entity\OrderInfo;
use App\Calendar\Entity\Schedule;
use App\Calendar\Form\CalendarEntryDeletionDto;
use App\Calendar\Form\CalendarEntryDto;
use App\Calendar\Form\DeletionReasonFormType;
use App\Calendar\Form\OrderInfoDto;
use App\Calendar\Form\OrderInfoType;
use App\Calendar\Form\ScheduleDto;
use App\Calendar\Form\ScheduleType;
use App\Calendar\Repository\CalendarEntryRepository;
use App\Calendar\View\Streamer;
use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
use App\Order\Entity\OrderId;
use App\Order\Enum\OrderStatus;
use function array_map;
use function array_merge;
use function assert;
use Closure;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use function is_string;
use Ramsey\Uuid\Uuid;
use function range;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CalendarEntryController extends AbstractController
{
    private Streamer $streamer;

    private CalendarEntryRepository $repository;

    public function __construct(Streamer $streamer, CalendarEntryRepository $repository)
    {
        $this->streamer = $streamer;
        $this->repository = $repository;
    }

    public function newAction(): Response
    {
        $date = $this->request->query->get('date');
        $date = null === $date
            ? new DateTimeImmutable('+1 hour', new DateTimeZone('+3 GTM'))
            : DateTimeImmutable::createFromFormat('Y-m-d H:i', $date);

        if (false === $date) {
            throw new BadRequestHttpException('Wrong date.');
        }

        $schedule = new ScheduleDto();
        $schedule->date = $date;
        $schedule->duration = new DateInterval('PT1H');

        $orderInfo = new OrderInfoDto();
        $orderId = $this->getIdentifier(OrderId::class);
        if ($orderId instanceof OrderId) {
            $orderView = $this->registry->view($orderId);

            $orderInfo->carId = $orderView['carId'];
            $orderInfo->customerId = $orderView['customerId'];

            if (null === $orderInfo->carId && null === $orderInfo->customerId) {
                $orderInfo->description = $this->display($orderId);
            }
        }

        $dto = new CalendarEntryDto(
            CalendarEntryId::generate(),
            $schedule,
            $orderInfo,
        );

        $orderId = $this->getIdentifier(OrderId::class);

        $form = $this->createFormBuilder($dto, [
            'attr' => [
                'class' => 'new-form',
            ],
        ])
            ->add('schedule', ScheduleType::class)
            ->add('orderInfo', OrderInfoType::class, [
                'disable_customer_and_car' => null !== $orderId,
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerId = $dto->orderInfo->customerId;

            $this->dispatchMessage(
                new CreateCalendarEntryCommand(
                    $dto->id,
                    new Schedule(
                        $dto->schedule->date,
                        $dto->schedule->duration,
                    ),
                    new OrderInfo(
                        $customerId,
                        $dto->orderInfo->carId,
                        $dto->orderInfo->description,
                        $dto->orderInfo->workerId,
                    ),
                )
            );

            if ($orderId instanceof OrderId) {
                $order = $this->registry->getBy(Order::class, $orderId);
                $order->setStatus(OrderStatus::scheduling());

                $this->em->persist(new EntryOrder($dto->id, $orderId));
                $this->em->flush();

                return $this->redirectToEasyPath('Order', 'show', [
                    'id' => $orderId->toString(),
                ]);
            }

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/calendar/new.html.twig', [
            'content_title' => 'Новая запись',
            'form' => $form->createView(),
        ]);
    }

    protected function listAction(): Response
    {
        $date = $this->request->query->get('date');
        $today = (new DateTimeImmutable())->setTime(0, 0, 0, 0);
        $date = null === $date
            ? $today
            : DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if (false === $date) {
            throw new BadRequestHttpException('Wrong date.');
        }

        $date = $date->setTime(0, 0, 0, 0);

        $orderId = $this->request->query->get('order_id');

        return $this->render('easy_admin/calendar/list.html.twig', [
            'date' => $date,
            'today' => $today,
            'streams' => $this->streamer->byDate($date),
            'orderId' => is_string($orderId) && Uuid::isValid($orderId) ? $orderId : null,
            'columns' => array_merge(...array_map(fn (int $val) => [$val.':00', $val.':30'], range(10, 21))),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createEditDto(Closure $closure): CalendarEntryDto
    {
        return CalendarEntryDto::fromView(
            $this->repository->view(CalendarEntryId::fromString($this->request->query->get('id')))
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof CalendarEntryDto);

        $this->dispatchMessage(
            new RescheduleCalendarEntryCommand(
                $dto->id,
                new Schedule(
                    $dto->schedule->date,
                    $dto->schedule->duration,
                ),
            )
        );

        $this->dispatchMessage(
            new ChangeOrderCalendarEntryCommand(
                $dto->id,
                new OrderInfo(
                    $dto->orderInfo->customerId,
                    $dto->orderInfo->carId,
                    $dto->orderInfo->description,
                    $dto->orderInfo->workerId,
                ),
            )
        );
    }

    protected function deletionAction(): Response
    {
        $view = $this->repository->view(CalendarEntryId::fromString($this->request->query->get('id')));
        $dto = new CalendarEntryDeletionDto($view->id);

        $form = $this->createFormBuilder($dto)
            ->add('reason', DeletionReasonFormType::class, [
                'label' => 'Причина',
                'expanded' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchMessage(
                new DeleteCalendarEntryCommand(
                    $dto->id,
                    $dto->reason,
                    $dto->description
                )
            );

            return $this->redirectToEasyPath('CalendarEntry', 'list');
        }

        return $this->render('easy_admin/calendar/deletion.html.twig', [
            'form' => $form->createView(),
            'entry' => $view,
        ]);
    }
}
