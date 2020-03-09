<?php

declare(strict_types=1);

namespace App\Controller\Admin\Report;

use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Order;
use function array_pop;
use function count;
use DateTime;
use Money\Currency;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ProfitController extends AbstractController
{
    private const DATETIME_FORMAT = 'Y-m-d\TH:i';

    /**
     * @Route("/profit", name="profit")
     */
    public function __invoke(Request $request, Registry $registry): Response
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
                 SELECT SUM((oip.price_amount::integer - COALESCE(oip.discount_amount, \'0\')::integer) * (oip.quantity::numeric / 100))
                 FROM order_item_part oip
                        JOIN order_item ON oip.id = order_item.id
                 WHERE order_item.order_id = o.id
               ) AS part_price,
               (
                 SELECT SUM(sub.price_amount::integer - COALESCE(sub.discount_amount, \'0\')::integer) 
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
                 SELECT SUM(operand_transaction.amount_amount::integer)
                 FROM operand_transaction
                        JOIN order_salary os on operand_transaction.id = os.transaction_id
                 WHERE os.order_id = o.id
               ) AS service_cost,
               (
                 SELECT SUM((sub.quantity)::numeric / 100 * sub.price::integer)
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

        $servicePrices = [];
        $serviceProfits = [];

        $partPrices = [];
        $partProfits = [];

        foreach ($orders as &$item) {
            $item['service_price'] = $servicePrice = new Money((int) $item['service_price'], new Currency('RUB'));
            $item['service_cost'] = $serviceCost = new Money((int) $item['service_cost'], new Currency('RUB'));
            $item['service_profit'] = $serviceProfit = $servicePrice->subtract($serviceCost);
            $item['service_profitability'] = $this->ratio($servicePrice, $serviceProfit);

            $servicePrices[] = $servicePrice;
            $serviceProfits[] = $serviceProfit;

            $item['part_price'] = $partPrice = new Money((int) $item['part_price'], new Currency('RUB'));
            $item['part_cost'] = $partCost = new Money((int) $item['part_cost'], new Currency('RUB'));
            $item['part_profit'] = $partProfit = $partPrice->subtract($partCost);
            $item['part_profitability'] = $this->ratio($partPrice, $partProfit);

            $partPrices[] = $partPrice;
            $partProfits[] = $partProfit;

            $item['customer'] = null !== $item['customer_id']
                ? $operandRepository->find($item['customer_id'])
                : null;
        }
        unset($item);

        $total = null;
        if (0 < count($orders)) {
            $servicePrice = $this->sum(...$servicePrices);
            $serviceProfit = $this->sum(...$serviceProfits);
            $partPrice = $this->sum(...$partPrices);
            $partProfit = $this->sum(...$partProfits);

            $total = [
                'service_price' => $servicePrice,
                'service_profit' => $serviceProfit,
                'service_profitability' => $this->ratio($servicePrice, $serviceProfit),
                'part_price' => $partPrice,
                'part_profit' => $partProfit,
                'part_profitability' => $this->ratio($partPrice, $partProfit),
            ];
        }

        return $this->render('admin/report/profit.html.twig', [
            'start' => $start,
            'end' => $end,
            'orders' => $orders,
            'total' => $total,
        ]);
    }

    private function sum(Money ...$collection): Money
    {
        $first = array_pop($collection);

        return Money::sum($first, ...$collection);
    }

    private function ratio(Money $left, Money $right): ?float
    {
        if (!$left->isPositive()) {
            return null;
        }

        return ((float) $right->ratioOf($left)) * 100;
    }
}
