<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;
use function sprintf;

/**
 * @ORM\Entity
 */
class MotionIncome extends Motion
{
    /**
     * @var IncomePart
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\IncomePart")
     * @ORM\JoinColumn(nullable=false)
     */
    private $incomePart;

    public function __construct(IncomePart $incomePart)
    {
        $this->incomePart = $incomePart;

        $description = sprintf('# Приход #%s', $incomePart->getIncome()->getId());

        parent::__construct($incomePart->getPart(), $incomePart->getQuantity(), $description);
    }
}
