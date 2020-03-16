<?php

namespace App\Calendar\Ports\EasyAdmin;

use App\Calendar\Application\Streamer;
use App\Calendar\Domain\CalendarEntry;
use App\Calendar\Domain\CalendarEntryId;
use App\Calendar\Domain\Command\CreateCalendarEntryCommand;
use App\Calendar\Domain\Command\DeleteCalendarEntryCommand;
use App\Calendar\Domain\Command\RescheduleCalendarEntryCommand;
use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Tenant\Employee;
use function array_map;
use function array_merge;
use function assert;
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
    protected function persistEntity($model): void
    {
        assert($model instanceof CalendarEntryDto);

        $this->commandBus->handle(
            new CreateCalendarEntryCommand(
                $model->date,
                $model->duration,
                $model->description,
                $model->worker,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function editAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $this->getDto(CalendarEntryId::fromString($this->request->query->get('id')));
        $this->request->attributes->set('easyadmin', $easyadmin);

        return parent::editAction();
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
            ->select('entity.id, entity.date, entity.duration, entity.description, IDENTITY(entity.worker) AS workerId')
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
            $item['date'],
            $item['duration'],
            $item['description'],
            $worker,
        );
    }
}
