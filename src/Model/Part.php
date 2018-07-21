<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Part extends Model
{
    /**
     * @var string
     */
    public $manufacturer;

    /**
     * @var string
     */
    public $number;

    /**
     * @var string
     */
    public $name;
}
