<?php

declare(strict_types=1);

namespace App\Partner\Ixora;

use App\Model\Part;
use App\Utils\StringUtils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Finder
{
    const IXORA_PART_FIND = '/soap/ApiService.asmx/Find';

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $number
     *
     * @return Part[]
     */
    public function search(string $number): array
    {
        if (StringUtils::isRussian($number) || false !== strpos(trim($number), ' ')) {
            return [];
        }

        try {
            $xml = $this->client->get(self::IXORA_PART_FIND, [
                'query' => [
                    'Number'      => $number,
                    'Maker'       => '',
                    'StockOnly'   => 'false',
                    'SubstFilter' => 'All',
                ],
            ])->getBody()->getContents();
        } catch (ServerException $e) {
            return [];
        }

        $elements = new \SimpleXMLElement($xml);

        $parts = [];
        foreach ($elements as $part) {
            $name = str_replace('  ', ' ', mb_convert_case(trim((string) $part->name), MB_CASE_TITLE));
            $key = sprintf(
                '%s-%s',
                $number = (string) $part->number,
                $manufacturer = (string) $part->maker
            );

            if (array_key_exists($key, $parts)) {
                continue;
            }

            $parts[$key] = new Part([
                'manufacturer' => $manufacturer,
                'number'       => $number,
                'name'         => $name,
            ]);
        }

        return array_values($parts);
    }
}
