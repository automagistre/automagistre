<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class JobItem
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cost", nullable=true)
     */
    private $cost;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Order", inversedBy="jobs")
     * @ORM\JoinColumn()
     */
    private $order;

    /**
     * @var JobAdvice
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Jobadvice")
     * @ORM\JoinColumn()
     */
    private $jobAdvice;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
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
     * @return string
     */
    public function getCost(): ?string
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
     * @return JobAdvice
     */
    public function getJobadvice(): ?JobAdvice
    {
        return $this->jobadvice;
    }

    /**
     * @param JobAdvice $jobadvice
     */
    public function setJobadvice(JobAdvice $jobadvice)
    {
        $this->jobadvice = $jobadvice;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getName(), $this->getCost());
    }
}
