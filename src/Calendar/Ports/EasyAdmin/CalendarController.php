<?php

namespace App\Calendar\Ports\EasyAdmin;

use App\Calendar\Application\Streamer;
use App\Calendar\Domain\CalendarEntry;
use App\Controller\EasyAdmin\AbstractController;
use function array_map;
use function array_merge;
use function assert;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use function range;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CalendarController extends AbstractController
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

        $entity = new CalendarEntryDto();
        $entity->date = $date;
        $entity->duration = new DateInterval('PT1H');

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($model): void
    {
        assert($model instanceof CalendarEntryDto);

        $entity = new CalendarEntry($model->date, $model->duration, $model->worker, $model->description);

        parent::persistEntity($entity);
    }
}
