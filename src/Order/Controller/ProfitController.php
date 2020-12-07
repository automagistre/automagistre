<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Customer\Entity\OperandId;
use App\Customer\Enum\CustomerTransactionSource;
use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
use App\Shared\Doctrine\Registry;
use function count;
use DateInterval;
use DateTimeImmutable;
use Money\Currency;
use Money\Money;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ProfitController extends AbstractController
{
    private const DATETIME_FORMAT = 'Y-m-d\TH:i';

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function indexAction(Request $request): Response
    {
        $registry = $this->registry;

        $start = $request->query->has('start')
            ? DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, $request->query->get('start'))
            : (new DateTimeImmutable('-1 week'))->setTime(0, 0);
        if (!$start instanceof DateTimeImmutable) {
            throw new BadRequestHttpException('Wrong date form of Start');
        }

        $end = $request->query->has('end')
            ? DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, $request->query->get('end'))
            : (new DateTimeImmutable())->setTime(23, 59, 59);
        if (!$end instanceof DateTimeImmutable) {
            throw new BadRequestHttpException('Wrong date form of End');
        }

        if ($start->getTimestamp() > $end->getTimestamp()) {
            [$start, $end] = [$end, $start];
        }

        $sql = '
            SELECT DATE(order_close_by.created_at) AS closed_at,
               o.id,
               o.number,
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
                 SELECT SUM(ct.amount_amount::integer)
                 FROM customer_transaction ct
                 WHERE o.id = ct.source_id
                    AND ct.source = :customer_transaction_source
               ) AS service_cost,
               (
                 SELECT SUM((sub.quantity)::numeric / 100 * sub.price::integer)
                 FROM (
                        SELECT oip.quantity AS quantity,
                               (
                                 SELECT ip.price_amount
                                 FROM income_part ip
                                        JOIN income i on ip.income_id = i.id
                                 WHERE i.accrued_at < o2closed.created_at
                                   AND ip.part_id = oip.part_id
                                 ORDER BY i.accrued_at DESC
                                 LIMIT 1
                               )                   AS price,
                               order_item.order_id AS order_id
                        FROM order_item_part oip
                               JOIN order_item ON oip.id = order_item.id
                               JOIN order_close ON order_close.order_id = order_item.order_id
                               JOIN created_by o2closed ON o2closed.id = order_close.id
                      ) sub
                 WHERE sub.order_id = o.id
               ) AS part_cost
            FROM orders o
            JOIN order_close ON o.id = order_close.order_id
            JOIN order_deal ON order_close.id = order_deal.id
            JOIN created_by order_close_by ON order_close_by.id = order_close.id
            WHERE order_close_by.created_at BETWEEN :start AND :end
            GROUP BY o.id, order_close_by.created_at
            ORDER BY order_close_by.created_at DESC
        ';

        $conn = $registry->manager(Order::class)->getConnection();

        $orders = $conn->fetchAllAssociative($sql, [
            'start' => $start->sub(new DateInterval('PT3H')), // TO UTC
            'end' => $end->sub(new DateInterval('PT3H')), // TO UTC
            'customer_transaction_source' => CustomerTransactionSource::orderSalary()->toId(),
        ], [
            'start' => 'datetime',
            'end' => 'datetime',
        ]);

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

            if (null !== $item['customer_id']) {
                $item['customer_id'] = OperandId::fromString($item['customer_id']);
            }
        }
        unset($item);

        $total = null;
        if (0 < count($orders)) {
            $servicePrice = Money::sum(...$servicePrices);
            $serviceProfit = Money::sum(...$serviceProfits);
            $partPrice = Money::sum(...$partPrices);
            $partProfit = Money::sum(...$partProfits);

            $total = [
                'service_price' => $servicePrice,
                'service_profit' => $serviceProfit,
                'service_profitability' => $this->ratio($servicePrice, $serviceProfit),
                'part_price' => $partPrice,
                'part_profit' => $partProfit,
                'part_profitability' => $this->ratio($partPrice, $partProfit),
            ];
        }

        return $this->render('easy_admin/order/report/profit.html.twig', [
            'start' => $start,
            'end' => $end,
            'orders' => $orders,
            'total' => $total,
        ]);
    }

    private function ratio(Money $left, Money $right): ?float
    {
        if (!$left->isPositive()) {
            return null;
        }

        return ((float) $right->ratioOf($left)) * 100;
    }
}
