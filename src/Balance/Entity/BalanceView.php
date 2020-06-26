<?php

declare(strict_types=1);

namespace App\Balance\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="balance_view")
 */
class BalanceView
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Embedded(class=Money::class, columnPrefix=false)
     */
    public Money $money;

    private function __construct(UuidInterface $id, Money $money)
    {
        $this->id = $id;
        $this->money = $money;
    }

    public static function sql(): string
    {
        return '
            CREATE VIEW balance_view AS
                SELECT 
                    o.id AS id, 
                    COALESCE(SUM(ct.amount_amount), 0) AS amount,
                    COALESCE(ct.amount_currency_code, \'RUB\') AS currency_code
                FROM operand o
                LEFT JOIN customer_transaction ct ON ct.operand_id = o.id
                GROUP BY o.id, ct.amount_currency_code
                UNION ALL
                SELECT 
                    w.id AS id, 
                    COALESCE(SUM(wt.amount_amount), 0) AS amount,
                    COALESCE(wt.amount_currency_code, \'RUB\') AS currency_code 
                FROM wallet w
                LEFT JOIN wallet_transaction wt ON wt.wallet_id = w.id
                GROUP BY w.id, wt.amount_currency_code
            ';
    }
}
