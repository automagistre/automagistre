<?php

declare(strict_types=1);

namespace App\Tenant;

use function getenv;
use function in_array;
use function is_string;
use Premier\Enum\Enum;
use function sprintf;

/**
 * @method bool   isDemo()
 * @method string toDisplayName()
 * @method string toIdentifier()
 * @method string toSmsOnScheduledEntry()
 * @method string toSmsOnReminderEntry()
 * @method static Tenant demo()
 * @method static Tenant msk()
 * @method static Tenant fromIdentifier(string $name)
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Tenant extends Enum
{
    private const DEMO = 0;
    private const MSK = 1;
    private const KAZAN = 2;
    private const SHAVLEV = 3;
    private const BUNKER = 4;

    protected static array $identifier = [
        self::DEMO => 'demo',
        self::MSK => 'msk',
        self::KAZAN => 'kazan',
        self::SHAVLEV => 'shavlev',
        self::BUNKER => 'bunker',
    ];

    protected static array $displayName = [
        self::DEMO => 'Демо',
        self::MSK => 'Автомагистр Москва',
        self::KAZAN => 'Автомагистр Казань',
        self::SHAVLEV => 'ИП Щавлев В.А.',
        self::BUNKER => 'Бункер Гараж',
    ];

    protected static array $requisites = [
        self::DEMO => [],
        self::MSK => [
            'type' => 'OOO',
            'name' => 'ООО "Автомагистр"',
            'address' => '115408, г.Москва, ул.Братеевская, д.16, к.1, кв. 345',
            'site' => 'www.automagistre.ru',
            'email' => 'info@automagistre.ru',
            'logo' => 'logo_automagistre_color.png',
            'telephones' => [
                '+7 (495) 984-81-82',
                '+7 (985) 929-40-87',
            ],
            'bank' => 'АО «Тинькофф Банк»',
            'ogrn' => '5137746189060',
            'inn' => '7725812690',
            'kpp' => '772401001',
            'rs' => '40702810810000060618',
            'ks' => '30101810145250000974',
            'bik' => '044525974',
            'guarantyUrl' => 'https://www.automagistre.ru/gr',
            'head' => 'Сидоров К.М.',
            'headType' => 'Генеральный директор',
        ],
        self::KAZAN => [
            'type' => 'IP',
            'name' => 'ИП Ахметзянов А.А.',
            'address' => 'г. Казань, Магистральная 33 к.1',
            'site' => 'www.automagistre.ru',
            'email' => 'info@automagistre.ru',
            'logo' => 'logo_automagistre_color.png',
            'telephones' => [
                '+7 (966) 260-10-90',
                '+7 (927) 244-48-68',
            ],
            'bank' => 'АО «Тинькофф Банк»',
            'ogrn' => '318169000126792',
            'inn' => '166017663015',
            'rs' => '40802810500000686477',
            'ks' => '30101810145250000974',
            'bik' => '044525974',
            'guarantyUrl' => 'https://www.automagistre.ru/gr',
            'head' => 'Ахметзянов А.А.',
            'headType' => 'Индивидуальный предприниматель',
        ],
        self::SHAVLEV => [
            'type' => 'IP',
            'name' => 'ИП Щавлев В.А.',
            'address' => 'Моск. обл., Орехово-Зуевский район, п. Пригородный, Малодубенское шоссе, 3 км, цех № 1',
            'site' => 'vk.com/smitavtoservis',
            'email' => null,
            'logo' => 'logo_smith.png',
            'telephones' => [
                //                '+7 (496) 423-43-18',
                '+7 (926) 214-56-65',
            ],
            'bank' => 'ПАО СБЕРБАНК',
            'ogrn' => null,
            'inn' => '507303160627',
            'rs' => '40802810940000009848',
            'ks' => '30101810400000000225',
            'bik' => '044525225',
            'guarantyUrl' => 'https://vk.com/topic-51443133_40629700',
            'head' => 'Щавлев В.А.',
            'headType' => 'Индивидуальный предприниматель',
        ],
        self::BUNKER => [
        ],
    ];

    protected static array $smsOnScheduledEntry = [
        self::DEMO => '',
        self::MSK => '{date} вас ожидают в ТехЦентре Автомагистр, по адресу Остаповский проезд, дом 17',
        self::KAZAN => '{date} вас ожидают в ТехЦентре Автомагистр, по адресу Магистральная улица, дом 33, корпус 1',
        self::SHAVLEV => '',
        self::BUNKER => '',
    ];

    protected static array $smsOnReminderEntry = [
        self::DEMO => '',
        self::MSK => 'Напоминаем, завтра в {time} вас ожидают в ТехЦентре Автомагистр. Пожалуйста, сообщите нам, если не можете приехать. +79859294087',
        self::KAZAN => 'Напоминаем, завтра в {time} вас ожидают в ТехЦентре Автомагистр. Пожалуйста, сообщите нам, если не можете приехать. +78432977760',
        self::SHAVLEV => '',
        self::BUNKER => '',
    ];

    public function getRequisites(): array
    {
        if ($this->isDemo()) {
            return self::$requisites[self::MSK];
        }

        return self::$requisites[$this->toId()];
    }

    public function isSmsEnabled(): bool
    {
        return in_array($this->toId(), [
            self::MSK,
            self::KAZAN,
        ], true);
    }

    public function toBusTopic(): string
    {
        return sprintf('%s_bus', $this->toIdentifier());
    }

    public static function isValid(string $identifier): bool
    {
        return in_array($identifier, self::$identifier, true);
    }

    public static function fromEnv(): self
    {
        $identifier = getenv('TENANT');

        if (!is_string($identifier)) {
            return self::demo();
        }

        return self::fromIdentifier($identifier);
    }
}
