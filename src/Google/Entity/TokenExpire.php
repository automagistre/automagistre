<?php

declare(strict_types=1);

namespace App\Google\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="google_review_token_expire")
 */
class TokenExpire
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\OneToOne(targetEntity=Token::class)
     */
    public Token $token;

    public function __construct(UuidInterface $id, Token $token)
    {
        $this->id = $id;
        $this->token = $token;
    }
}
