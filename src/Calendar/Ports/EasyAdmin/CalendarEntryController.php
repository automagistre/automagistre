<?php

namespace App\Calendar\Ports\EasyAdmin;

use App\Calendar\Application\Streamer;
use App\Calendar\Domain\CalendarEntry;
use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Tenant\Employee;
use function array_map;
use function array_merge;
use function assert;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use function range;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CalendarEntryController extends AbstractController
{
    private Streamer $streamer;

    public function __construct(Streamer $streamer)
    {
        $this->streamer = $streamer;
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

        $entity = new CalendarEntry(
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
        $row = $this->em->createQueryBuilder()
            ->select('entity.id, entity.date, entity.duration, entity.description, IDENTITY(entity.worker) AS workerId')
            ->from(CalendarEntry::class, 'entity')
            ->where('entity.id = :id')
            ->getQuery()
            ->setParameter('id', $this->request->query->get('id'))
            ->getSingleResult();

        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = new CalendarEntryDto(
            $row['id'],
            $row['date'],
            $row['duration'],
            $row['description'],
            null !== $row['workerId'] ? $this->em->getRepository(Employee::class)->find($row['workerId']) : null,
        );
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

        parent::updateEntity(
            new CalendarEntry(
                $dto->date,
                $dto->duration,
                $this->getUser()->uuid,
                $dto->worker,
                $dto->description,
                $dto->id
            )
        );
    }
}
