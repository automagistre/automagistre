<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Part
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

    public function __construct(string $manufacturer, string $number, string $name)
    {
        $this->manufacturer = $manufacturer;
        $this->number = $number;
        $this->name = $name;
    }
}
