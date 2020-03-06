<?php

namespace App\Appointment\Controller;

use App\Appointment\Entity\Appointment;
use App\Appointment\View\Streamer;
use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Tenant\Employee;
use App\Entity\Tenant\Order;
use function array_map;
use function array_merge;
use function assert;
use DateTimeImmutable;
use DateTimeZone;
use function range;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class AppointmentController extends AbstractController
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

        return $this->render('easy_admin/appointment/list.html.twig', [
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

        $entity = new Appointment();
        $entity->date = $date;

        $entity->order = new Order();

        $this->getEntity(Employee::class, static function (Employee $worker) use ($entity): void {
            $entity->order->setWorker($worker);
        });

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        assert($entity instanceof Appointment);

        $this->em->persist($entity->order);

        parent::persistEntity($entity);
    }
}
