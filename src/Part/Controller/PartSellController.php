<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\Doctrine\Registry;
use App\EasyAdmin\Controller\AbstractController;
use App\Part\Entity\PartView;
use App\Storage\Entity\Motion;
use App\Storage\Enum\MotionType;
use App\Tenant\State;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function array_map;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PartSellController extends AbstractController
{
    private const DATETIME_FORMAT = 'Y-m-d\TH:i';

    public function __construct(Registry $registry, private State $state)
    {
        $this->registry = $registry;
    }

    public function indexAction(Request $request): Response
    {
        $registry = $this->registry;

        $start = $request->query->has('start')
            ? DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, (string) $request->query->get('start'))
            : (new DateTimeImmutable('-1 day'))->setTime(0, 0);

        if (!$start instanceof DateTimeImmutable) {
            throw new BadRequestHttpException('Wrong date form of Start');
        }

        $end = $request->query->has('end')
            ? DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, (string) $request->query->get('end'))
            : (new DateTimeImmutable('now'))->setTime(23, 59, 59);

        if (!$end instanceof DateTimeImmutable) {
            throw new BadRequestHttpException('Wrong date form of End');
        }

        if ($start->getTimestamp() > $end->getTimestamp()) {
            [$start, $end] = [$end, $start];
        }

        $sql = '
            SELECT m.part_id,
                   ABS(ROUND(SUM(m.quantity::NUMERIC), 2)) AS quantity
            FROM motion m
            WHERE m.created_at BETWEEN :start AND :end
              AND m.tenant_id = :tenant
            AND m.source_type = :source_order
            GROUP BY m.part_id
            ORDER BY quantity DESC, m.part_id
        ';

        $conn = $registry->manager(Motion::class)->getConnection();

        $items = $conn->fetchAllAssociative($sql, [
            'start' => $start->sub(new DateInterval('PT3H')), // TO UTC
            'end' => $end->sub(new DateInterval('PT3H')), // TO UTC
            'source_order' => MotionType::order(),
            'tenant' => $this->state->require()->toId(),
        ], [
            'start' => 'datetime',
            'end' => 'datetime',
            'source_order' => 'motion_source_enum',
        ]);

        $ids = array_map(static fn (array $item): string => $item['part_id'], $items);

        $parts = $registry->manager(PartView::class)->createQueryBuilder()
            ->select('part')
            ->from(PartView::class, 'part', 'part.id')
            ->where('part.id IN (:ids)')
            ->getQuery()
            ->setParameter('ids', $ids)
            ->getResult()
        ;

        return $this->render('easy_admin/part/report/sell.html.twig', [
            'start' => $start,
            'end' => $end,
            'items' => $items,
            'parts' => $parts,
        ]);
    }
}
