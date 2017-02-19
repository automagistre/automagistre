<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Messagesource
 *
 * @ORM\Table(name="messagesource", uniqueConstraints={@ORM\UniqueConstraint(name="source_category_Index", columns={"category", "source"})})
 * @ORM\Entity
 */
class Messagesource
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=30, nullable=true)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="blob", length=65535, nullable=true)
     */
    private $source;

}

