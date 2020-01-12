<?php

namespace App\Appointment\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Tenant\Order;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Appointment
{
    use Identity;
    use CreatedAt;
    use CreatedByRelation;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\Order")
     */
    public ?Order $order = null;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="datetime_immutable")
     */
    public ?DateTimeImmutable $date = null;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="dateinterval")
     */
    public ?DateInterval $duration = null;

    public function __construct()
    {
        $this->date = new DateTimeImmutable();
    }
}
