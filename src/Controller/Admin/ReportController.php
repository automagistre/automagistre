<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Order;
use DateTime;
use Money\Currency;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/report", name="report_")
 */
final class ReportController extends AbstractController
{
    private const DATETIME_FORMAT = 'Y-m-d\TH:i';

    /**
     * @Route("/profit", name="profit")
     */
    public function profit(Request $request, Registry $registry): Response
    {
        $start = $request->query->has('start')
            ? DateTime::createFromFormat(self::DATETIME_FORMAT, $request->query->get('start'))
            : new DateTime('-1 week');
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
            SELECT DATE(o.closed_at) AS closed_at,
               o.id,
               o.customer_id,
               (
                 SELECT SUM((oip.price_amount - IFNULL(oip.discount_amount, 0)) * (oip.quantity / 100))
                 FROM order_item_part oip
                        JOIN order_item ON oip.id = order_item.id
                 WHERE order_item.order_id = o.id
               ) AS part_price,
               (
                 SELECT SUM(sub.price_amount - IFNULL(sub.discount_amount, 0)) 
                 FROM (
                   SELECT ois.price_amount AS price_amount, 
                          ois.discount_amount AS discount_amount, 
                          order_item.order_id AS order_id 
                    FROM order_item_service ois
                        JOIN order_item ON ois.id = order_item.id
                   ) sub
                 WHERE sub.order_id = o.id
               ) AS service_price,
               (
                 SELECT SUM(operand_transaction.amount_amount)
                 FROM operand_transaction
                        JOIN order_salary os on operand_transaction.id = os.transaction_id
                 WHERE os.order_id = o.id
               ) AS salary,
               (
                 SELECT SUM((sub.quantity / 100) * sub.price)
                 FROM (
                        SELECT oip.quantity AS quantity,
                               (
                                 SELECT ip.price_amount
                                 FROM income_part ip
                                        JOIN income i on ip.income_id = i.id
                                 WHERE i.accrued_at < o2.closed_at
                                   AND ip.part_id = oip.part_id
                                 ORDER BY i.accrued_at DESC
                                 LIMIT 1
                               )            AS price,
                               o2.id AS order_id
                        FROM order_item_part oip
                               JOIN order_item ON oip.id = order_item.id
                               JOIN orders o2 on order_item.order_id = o2.id
                      ) sub
                 WHERE sub.order_id = o.id
               ) AS part_cost
            FROM orders o
            WHERE o.closed_at BETWEEN :start AND :end
            GROUP BY o.id
            ORDER BY o.closed_at DESC
        ';

        $conn = $registry->manager(Order::class)->getConnection();

        $orders = $conn->fetchAll($sql, [
            'start' => $start->setTime(0, 0),
            'end' => $end->setTime(23, 59, 59),
        ], [
            'start' => 'datetime',
            'end' => 'datetime',
        ]);

        $operandRepository = $registry->repository(Operand::class);

        return $this->render('admin/report/profit.html.twig', [
            'start' => $start,
            'end' => $end,
            'orders' => \array_map(function (array $item) use ($operandRepository) {
                foreach (['service_price', 'salary', 'part_price', 'part_cost'] as $property) {
                    $item[$property] = new Money((int) $item[$property], new Currency('RUB'));
                }

                $item['customer'] = null !== $item['customer_id']
                    ? $operandRepository->find($item['customer_id'])
                    : null;

                return $item;
            }, $orders),
        ]);
    }
}
