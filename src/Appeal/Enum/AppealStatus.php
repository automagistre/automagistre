<?php

declare(strict_types=1);

namespace App\Appeal\Enum;

use Premier\Enum\Enum;

/**
 * @method string toDisplayName()
 * @method static self new()
 * @method static self inWork()
 * @method static self postponed()
 * @method static self closed()
 */
final class AppealStatus extends Enum
{
    private const NEW = 1;
    private const IN_WORK = 2;
    private const POSTPONED = 3;
    private const CLOSED = 4;

    protected static array $displayName = [
        self::NEW => 'Новая',
        self::IN_WORK => 'В работе',
        self::POSTPONED => 'Отложена',
        self::CLOSED => 'Закрыта',
    ];

    protected static array $color = [
        self::NEW => 'danger',
        self::IN_WORK => 'warning',
        self::POSTPONED => 'default',
        self::CLOSED => 'default',
    ];

    public function next(): ?self
    {
        switch ($this->toId()) {
            case self::CLOSED:
            case self::NEW:
                return self::create(self::IN_WORK);
            case self::IN_WORK:
                return self::create(self::CLOSED);
        }

        return null;
    }
}
