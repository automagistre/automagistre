<?php

namespace App\Calendar\Controller;

use App\Calendar\Application\Create\CreateCalendarEntryCommand;
use App\Calendar\Application\Delete\DeleteCalendarEntryCommand;
use App\Calendar\Application\Reschedule\RescheduleCalendarEntryCommand;
use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Form\CalendarEntryDeletionDto;
use App\Calendar\Form\CalendarEntryDto;
use App\Calendar\Form\DeletionReasonFormType;
use App\Calendar\View\Streamer;
use App\Controller\EasyAdmin\AbstractController;
use App\Employee\Entity\Employee;
use function array_map;
use function array_merge;
use function assert;
use Closure;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use function range;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CalendarEntryController extends AbstractController
{
    private Streamer $streamer;

    private CommandBus $commandBus;

    public function __construct(Streamer $streamer, CommandBus $commandBus)
    {
        $this->streamer = $streamer;
        $this->commandBus = $commandBus;
    }

    protected function listAction(): Response
    {
        $date = $this->request->query->get('date');
        $today = new DateTimeImmutable();
        $date = null === $date ? $today : DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if (false === $date) {
            throw new BadRequestHttpException('Wrong date.');
        }

        return $this->render('easy_admin/calendar/list.html.twig', [
            'date' => $date,
            'today' => $today,
            'streams' => $this->streamer->byDate($date),
            'columns' => array_merge(...array_map(fn (int $val) => [$val.':00', $val.':30'], range(10, 21))),
        ]);
    }

    protected function createNewEntity()
    {
        $date = $this->request->query->get('date');
        $date = null === $date
            ? new DateTimeImmutable('+1 hour', new DateTimeZone('+3 GTM'))
            : DateTimeImmutable::createFromFormat('Y-m-d H:i', $date);

        if (false === $date) {
            throw new BadRequestHttpException('Wrong date.');
        }

        return new CalendarEntryDto(
            null,
            $date,
            new DateInterval('PT1H')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof CalendarEntryDto);

        $this->commandBus->handle(
            new CreateCalendarEntryCommand(
                $dto->date,
                $dto->duration,
                $dto->firstName,
                $dto->lastName,
                $dto->phone,
                $dto->carId,
                $dto->description,
                $dto->worker,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createEditDto(Closure $closure): ?object
    {
        return $this->getDto(CalendarEntryId::fromString($this->request->query->get('id')));
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof CalendarEntryDto);

        $this->commandBus->handle(
            new RescheduleCalendarEntryCommand(
                $dto->id,
                $dto->date,
                $dto->duration,
                $dto->firstName,
                $dto->lastName,
                $dto->phone,
                $dto->carId,
                $dto->description,
                $dto->worker,
            )
        );
    }

    protected function deletionAction(): Response
    {
        $id = CalendarEntryId::fromString($this->request->query->get('id'));
        $dto = new CalendarEntryDeletionDto($id);

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
            $this->commandBus->handle(
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
            'item' => $this->getDto($id),
        ]);
    }

    private function getDto(CalendarEntryId $id): CalendarEntryDto
    {
        $item = $this->em->createQueryBuilder()
            ->select('entity.id')
            ->addSelect('entity.schedule.date')
            ->addSelect('entity.schedule.duration')
            ->addSelect('entity.customer.firstName AS firstName')
            ->addSelect('entity.customer.lastName AS lastName')
            ->addSelect('entity.customer.phone AS phone')
            ->addSelect('entity.customer.carId AS carId')
            ->addSelect('entity.customer.description AS description')
            ->addSelect('IDENTITY(entity.worker) AS workerId')
            ->from(CalendarEntry::class, 'entity')
            ->where('entity.id = :id')
            ->getQuery()
            ->setParameter('id', $id)
            ->getSingleResult();

        /** @var Employee|null $worker */
        $worker = null !== $item['workerId']
            ? $this->em->getRepository(Employee::class)->find($item['workerId'])
            : null;

        return new CalendarEntryDto(
            $item['id'],
            $item['schedule.date'],
            $item['schedule.duration'],
            $item['firstName'],
            $item['lastName'],
            $item['phone'],
            $item['carId'],
            $item['description'],
            $worker,
        );
    }
}
