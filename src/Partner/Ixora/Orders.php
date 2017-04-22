<?php

declare(strict_types=1);

namespace App\Partner\Ixora;

use App\Model\Supply;
use App\Model\SupplyItem;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Money\Currency;
use Money\Money;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * @see http://ws.ixora-auto.ru/soap/ApiService.asmx?op=OrderStatusGet
 *
 * Statuses:
 *      All
 *      InWork
 *      InOrder
 *      Ordered
 *      Purchased
 *      OnTheWay
 *      ToIssue
 *      Issued
 *      NotAvailable
 *      Reserve
 *      Acceptance
 *      Moving
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Orders
{
    const IXORA_ORDERS = '/soap/ApiService.asmx/OrderStatusGet';
    const DATE_FORMAT = 'Y-m-d\TH:i:s';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    public function __construct(Client $client, DecoderInterface $decoder)
    {
        $this->client = $client;
        $this->decoder = $decoder;
    }

    /**
     * @param string         $status
     * @param \DateTime|null $dateFrom
     *
     * @return Supply[]
     */
    public function find($status = 'All', \DateTime $dateFrom = null)
    {
        if (!$dateFrom) {
            $dateFrom = new \DateTime();
        }

        try {
            $xml = $this->client->get(self::IXORA_ORDERS, [
                'query' => [
                    'Number'    => '',
                    'Reference' => '',
                    'status'    => $status,
                    'dateFrom'  => $dateFrom->format('Y-m-d'),
                ],
            ])->getBody()->getContents();
        } catch (ServerException $e) {
            return [];
        }

        $orders = $this->decoder->decode($xml, 'xml')['Data']['OrderStatus'];

        return array_map(function (array $item) {
            return new Supply([
                'id'                => $item['Id'],
                'date'              => \DateTime::createFromFormat(self::DATE_FORMAT, $item['Date']),
                'status'            => $item['Status'],
                'items'             => array_map(function (array $item) {
                    return new SupplyItem([
                        'number'       => $item['DetailNumber'],
                        'manufacturer' => $item['DetailMaker'],
                        'name'         => $item['DetailName'],
                        'price'        => new Money($item['Price'] * 100, new Currency('RUB')),
                        'quantity'     => $item['Ordered'] * 100,
                    ]);
                }, $item['Items']),
                'arrivalOrientAt'   => \DateTime::createFromFormat(self::DATE_FORMAT, $item['DateArrivalOrient']),
                'arrivalWarrantyAt' => \DateTime::createFromFormat(self::DATE_FORMAT, $item['DateArrivalWarranty']),
            ]);
        }, $orders);
    }

    public static function getSupplierName(): string
    {
        return 'ixora';
    }
}
