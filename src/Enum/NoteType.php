<?php

declare(strict_types=1);

namespace App\Enum;

use Grachevko\Enum\Enum;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NoteType extends Enum
{
    protected const SUCCESS = 1;
    protected const INFO = 2;
    protected const WARNING = 3;
    protected const DANGER = 4;

    /**
     * @var array
     */
    protected static $name = [
        self::SUCCESS => 'Лучи добра',
        self::INFO => 'Информация',
        self::WARNING => 'Внимание',
        self::DANGER => 'Тревога',
    ];
}
