<?php

declare(strict_types=1);

namespace App\Enum;

use Grachevko\Enum\Enum;

/**
 * @method string getDisplayName()
 * @method static self msk()
 * @method static self fromIdentifier(string $name)
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Tenant extends Enum
{
    private const MSK = 1;
    private const KAZAN = 2;

    /**
     * @var array
     */
    protected static $identifier = [
        self::MSK => 'msk',
        self::KAZAN => 'kazan',
    ];

    /**
     * @var array
     */
    protected static $displayName = [
        self::MSK => 'Москва',
        self::KAZAN => 'Казань',
    ];

    public function getDatabase(): string
    {
        return \sprintf('tenant_%s', $this->getName());
    }

    public function getIdentifier(): string
    {
        return self::$identifier[$this->getId()];
    }

    public static function isValid(string $identifier): bool
    {
        return \in_array($identifier, self::$identifier, true);
    }
}
