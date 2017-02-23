<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="jobitem", indexes={@ORM\Index(name="_order_id", columns={"_order_id"})})
 * @ORM\Entity
 */
class Jobitem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="_user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cost", type="string", length=255, nullable=true)
     */
    private $cost;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Order", inversedBy="jobs")
     * @ORM\JoinColumn(name="_order_id")
     */
    private $order;

    /**
     * @var Jobadvice
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Jobadvice")
     * @ORM\JoinColumn()
     */
    private $jobadvice;

    /**
     * @var bool
     *
     * @ORM\Column(name="employee__user_id", type="boolean", nullable=true)
     */
    private $employeeUserId;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getCost(): ?int
    {
        return $this->cost;
    }

    /**
     * @param string $cost
     */
    public function setCost(string $cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return Order
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return Jobadvice
     */
    public function getJobadvice(): ?Jobadvice
    {
        return $this->jobadvice;
    }

    /**
     * @param Jobadvice $jobadvice
     */
    public function setJobadvice(Jobadvice $jobadvice)
    {
        $this->jobadvice = $jobadvice;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getName(), $this->getCost());
    }
}
