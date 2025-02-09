<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Event\AppealCreated;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="appeal_question")
 */
class Question extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
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
     * @ORM\Column(type="text")
     */
    public string $question;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(AppealId $id, string $name, string $email, string $question)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->question = $question;

        $this->record(new AppealCreated($this->id));
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
