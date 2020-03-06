<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation as CreatedBy;
use App\Entity\Superclass\Note;
use App\Enum\NoteType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class OrderNote extends Note
{
    use CreatedBy;

    /**
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\Order")
     */
    public ?Order $order = null;

    public function __construct(Order $order, NoteType $noteType = null, string $text = null)
    {
        parent::__construct($noteType, $text);

        $this->order = $order;
    }
}
