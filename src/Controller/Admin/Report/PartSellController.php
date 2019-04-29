<?php

declare(strict_types=1);

namespace App\Controller\Admin\Report;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\Motion;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartSellController extends AbstractController
{
    private const DATETIME_FORMAT = 'Y-m-d\TH:i';

    /**
     * @Route("/part-sell", name="part_sell")
     */
    public function __invoke(Request $request, Registry $registry): Response
    {
        $start = $request->query->has('start')
            ? DateTime::createFromFormat(self::DATETIME_FORMAT, $request->query->get('start'))
            : new DateTime('-1 month');
        if (!$start instanceof DateTime) {
            throw new BadRequestHttpException('Wrong date form of Start');
        }

        $end = $request->query->has('end')
            ? DateTime::createFromFormat(self::DATETIME_FORMAT, $request->query->get('end'))
            : new DateTime();
        if (!$end instanceof DateTime) {
            throw new BadRequestHttpException('Wrong date form of End');
        }

        if ($start->getTimestamp() > $end->getTimestamp()) {
            [$start, $end] = [$end, $start];
        }

        $sql = '
            SELECT m.part_id,
                   SUM(CAST(ABS(m.quantity) / 100 AS INTEGER))                                                     AS quantity,
                   (SELECT CAST(SUM(sub.quantity / 100) AS INTEGER) FROM motion sub WHERE sub.part_id = m.part_id) AS stock
            FROM motion m
                     INNER JOIN motion_order mo on m.id = mo.id
            WHERE m.created_at BETWEEN :start AND :end
            GROUP BY m.part_id
            ORDER BY NULL
        ';

        $conn = $registry->manager(Motion::class)->getConnection();

        $items = $conn->fetchAll($sql, [
            'start' => $start->setTime(0, 0),
            'end' => $end->setTime(23, 59, 59),
        ], [
            'start' => 'datetime',
            'end' => 'datetime',
        ]);

        $ids = \array_map(static function (array $item) {
            return $item['part_id'];
        }, $items);

        $parts = $registry->manager(Part::class)->createQueryBuilder()
            ->select('part')
            ->from(Part::class, 'part', 'part.id')
            ->where('part.id IN (:ids)')
            ->getQuery()
            ->setParameter('ids', $ids)
            ->getResult();

        \usort($items, static function (array $left, array $right) {
            return (int) $left['quantity'] < (int) $right['quantity'];
        });

        return $this->render('admin/report/part_sell.html.twig', [
            'start' => $start,
            'end' => $end,
            'items' => $items,
            'parts' => $parts,
        ]);
    }
}
