<?php

declare(strict_types=1);

namespace App\Enum;

use Grachevko\Enum\Enum;

/**
 * @method bool isSandbox()
 * @method string getDisplayName()
 * @method static self msk()
 * @method static self fromIdentifier(string $name)
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Tenant extends Enum
{
    private const SANDBOX = 0;
    private const MSK = 1;
    private const KAZAN = 2;

    /**
     * @var array
     */
    protected static $identifier = [
        self::SANDBOX => 'sandbox',
        self::MSK => 'msk',
        self::KAZAN => 'kazan',
    ];

    /**
     * @var array
     */
    protected static $displayName = [
        self::SANDBOX => 'Песочница',
        self::MSK => 'Москва',
        self::KAZAN => 'Казань',
    ];

    /**
     * @var array
     */
    protected static $requisites = [
        self::SANDBOX => [],
        self::MSK => [
            'name' => 'ООО "Автомагистр"',
            'address' => 'г. Москва, ул. Люблинская, д. 31/1',
            'telephones' => [
                '+7 (495) 984-81-82',
                '+7 (985) 929-40-87',
            ],
            'bank' => 'АО «Тинькофф Банк»',
            'ogrn' => '5137746189060',
            'inn' => '7725812690',
            'kpp' => '772301001',
            'rs' => '40702810202000000371',
            'ks' => '30101810800000000427',
            'bik' => '044525974',
        ],
        self::KAZAN => [
            'name' => 'ИП Ахметзянов А.А.',
            'address' => 'г. Казань, Магистральная 33 к.1',
            'telephones' => [
                '+7 (843) 297-77-60',
                '+7 (843) 297-77-61',
            ],
            'bank' => 'АО «Тинькофф Банк»',
            'ogrn' => '318169000126792',
            'inn' => '166017663015',
            'rs' => '40802810500000686477',
            'ks' => '30101810145250000974',
            'bik' => '044525974',
        ],
    ];

    public function getIdentifier(): string
    {
        return self::$identifier[$this->getId()];
    }

    public function getRequisites(): array
    {
        if ($this->isSandbox()) {
            return self::$requisites[self::MSK];
        }

        return self::$requisites[$this->getId()];
    }

    public static function isValid(string $identifier): bool
    {
        return \in_array($identifier, self::$identifier, true);
    }
}
