<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Motion
{
    use Identity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     * @ORM\JoinColumn
     */
    private $part;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order")
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $description;

    public function __construct(Part $part, int $quantity, Order $order = null)
    {
        $this->part = $part;
        $this->quantity = $quantity;
        $this->order = $order;
    }

    public static function createFromIncomePart(IncomePart $incomePart): self
    {
        $motion = new self($incomePart->getPart(), $incomePart->getQuantity());
        $motion->description = sprintf('# Приход #%s', $incomePart->getIncome()->getId());

        return $motion;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPart(): Part
    {
        return $this->part;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
