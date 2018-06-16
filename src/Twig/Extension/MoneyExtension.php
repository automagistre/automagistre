<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Money\Money;
use Money\MoneyFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class MoneyExtension extends AbstractExtension
{
    /**
     * @var MoneyFormatter
     */
    private $formatter;

    public function __construct(MoneyFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('localize_money', [$this, 'localizeMoney']),
        ];
    }

    public function localizeMoney(Money $money): string
    {
        return $this->formatter->format($money);
    }
}
