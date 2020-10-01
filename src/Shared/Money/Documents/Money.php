<?php

declare(strict_types=1);

namespace App\Shared\Money\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;

/**
 * @EmbeddedDocument
 */
class Money
{
    /**
     * @Field()
     */
    public string $amount;

    /**
     * @Field()
     */
    public string $currency;

    public function __construct(string $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function fromMoney(\Money\Money $money): self
    {
        return new self($money->getAmount(), $money->getCurrency()->getCode());
    }
}
