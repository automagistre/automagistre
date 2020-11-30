<?php

declare(strict_types=1);

namespace App\CreatedBy\Entity;

use App\User\Entity\UserView;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="created_by_view")
 */
class CreatedByView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="user_view")
     */
    public UserView $by;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $at;

    public function __construct(UuidInterface $id, UserView $user, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->by = $user;
        $this->at = $createdAt;
    }

    public static function sql(): string
    {
        return '
            CREATE VIEW created_by_view AS
            SELECT 
                cb.id,
                CONCAT_WS(
                    \',\', 
                    u.id, 
                    u.username, 
                    COALESCE(u.last_name, \'\'), 
                    COALESCE(u.first_name, \'\')
                ) AS by,
                cb.created_at AS at
            FROM created_by cb
                JOIN users u ON u.id = cb.user_id
        ';
    }
}
