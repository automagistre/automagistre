<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="customer_view")
 */
class CustomerView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="operand_id")
     */
    public OperandId $id;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $name;

    /**
     * @ORM\Column(type="money")
     */
    public Money $balance;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $email = null;

    /**
     * @ORM\Column(type="phone_number")
     */
    public ?PhoneNumber $telephone = null;

    private function __construct(OperandId $id, string $name, Money $balance, ?string $email, ?PhoneNumber $telephone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->balance = $balance;
        $this->email = $email;
        $this->telephone = $telephone;
    }

    public static function sql(): string
    {
        return <<<'SQL'
            CREATE VIEW customer_view AS
            SELECT o.id,
                   CASE
                       WHEN org IS NOT NULL
                           THEN org.name
                       ELSE p.lastname || ' ' || p.lastname
                       END                          AS name,
                   COALESCE(balance.money, 'RUB 0') AS balance,
                   o.email,
                   CASE WHEN org IS NOT NULL THEN org.telephone ELSE p.telephone END AS telephone
            FROM operand o
                     LEFT JOIN organization org ON o.id = org.id
                     LEFT JOIN person p ON o.id = p.id
                     LEFT JOIN (
                        SELECT o.id                                                    AS id,
                               ct.amount_currency_code || ' ' || SUM(ct.amount_amount) AS money
                        FROM operand o
                                 LEFT JOIN customer_transaction ct ON ct.operand_id = o.id
                        GROUP BY o.id, ct.amount_currency_code
                    ) balance ON balance.id = o.id
            SQL;
    }
}
