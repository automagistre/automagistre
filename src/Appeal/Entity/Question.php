<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_question")
 */
class Question
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
     * @ORM\Column
     */
    public string $email;

    /**
     * @ORM\Column
     */
    public string $question;

    public function __construct(AppealId $id, string $name, string $email, string $question)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->question = $question;
    }

    public static function create(string $name, string $email, string $question): self
    {
        return new self(
            AppealId::generate(),
            $name,
            $email,
            $question,
        );
    }
}
