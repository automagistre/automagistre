<?php

namespace App\Calendar\Ports\EasyAdmin;

use App\Calendar\Application\Streamer;
use App\Calendar\Domain\CalendarEntry;
use App\Calendar\Domain\CalendarEntryId;
use App\Calendar\Domain\CalendarEntryRepository;
use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Tenant\Employee;
use function array_map;
use function array_merge;
use function assert;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use function range;
use function sprintf;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CalendarEntryController extends AbstractController
{
    private Streamer $streamer;

    private CalendarEntryRepository $repository;

    public function __construct(Streamer $streamer, CalendarEntryRepository $repository)
    {
        $this->streamer = $streamer;
        $this->repository = $repository;
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

        $entity = CalendarEntry::create(
            $model->date,
            $model->duration,
            $this->getUser()->uuid,
            $model->worker,
            $model->description
        );

        parent::persistEntity($entity);
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

        $previous = $this->em->getRepository(CalendarEntry::class)->find($dto->id);
        assert($previous instanceof CalendarEntry);

        $entity = $previous->reschedule(
            $dto->date,
            $dto->duration,
            $this->getUser()->uuid,
            $dto->worker,
            $dto->description
        );

        parent::updateEntity($entity);
    }

    protected function deletionAction(): Response
    {
        $id = CalendarEntryId::fromString($this->request->query->get('id'));

        $entity = $this->repository->get($id);
        if (null === $entity) {
            throw new NotFoundHttpException(sprintf('CalendarEntry with id "%s" not found.', $id->toString()));
        }

        $dto = new CalendarEntryDeletionDto();

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
            $entity->delete($dto->reason, $dto->description, $this->getUser()->uuid);

            $this->em->flush();

            return $this->redirectToEasyPath('CalendarEntry', 'list');
        }

        return $this->render('easy_admin/calendar/deletion.html.twig', [
            'form' => $form->createView(),
            'item' => $row = $this->em->createQueryBuilder()
                ->select('entity.id, entity.date, entity.duration, entity.description')
                ->from(CalendarEntry::class, 'entity')
                ->where('entity.id = :id')
                ->getQuery()
                ->setParameter('id', $id)
                ->getSingleResult(),
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
