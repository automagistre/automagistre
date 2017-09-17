<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Money\Money;
use Money\MoneyFormatter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class MoneyExtension extends \Twig_Extension
{
    /**
     * @var MoneyFormatter
     */
    private $formatter;

    public function __construct(MoneyFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('localize_money', [$this, 'localizeMoney']),
        ];
    }

    public function localizeMoney(Money $money): string
    {
        return $this->formatter->format($money);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'app_money';
    }
}
