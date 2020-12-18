<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Enum\AppealStatus;
use App\Appeal\Enum\AppealType;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity
 * @ORM\Table(name="appeal_view")
 *
 * @psalm-suppress MissingConstructor
 */
class AppealView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="appeal_id")
     */
    public AppealId $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="appeal_type")
     */
    public AppealType $type;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     */
    public ?PhoneNumber $phone = null;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $email = null;

    /**
     * @ORM\Column(type="appeal_status")
     */
    public AppealStatus $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function toId(): AppealId
    {
        return $this->id;
    }

    public static function sql(): string
    {
        return <<<'SQL'
            CREATE VIEW appeal_view AS
            SELECT appeal.id,
                   appeal.name,
                   appeal.type,
                   appeal.phone,
                   appeal.email,
                   COALESCE(status.status, 1) AS status,
                   created_at.created_at
            FROM (
                     SELECT id, name, 1 AS type, phone, null AS email
                     FROM appeal_calculator
                     UNION ALL
                     SELECT id, name, 2 AS type, phone, null AS email
                     FROM appeal_cooperation
                     UNION ALL
                     SELECT id, name, 3 AS type, null AS phone, email
                     FROM appeal_question
                     UNION ALL
                     SELECT id, name, 4 AS type, phone, null AS email
                     FROM appeal_schedule
                     UNION ALL
                     SELECT id, name, 5 AS type, phone, null AS email
                     FROM appeal_tire_fitting
                 ) appeal
                     LEFT JOIN LATERAL (SELECT *
                                        FROM appeal_status sub
                                        WHERE sub.appeal_id = appeal.id
                                        ORDER BY sub.id DESC
                                        LIMIT 1
                ) status ON TRUE
                     JOIN created_at ON created_at.id = appeal.id
            SQL;
    }
}
