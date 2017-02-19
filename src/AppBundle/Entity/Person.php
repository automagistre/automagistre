<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 *
 * @ORM\Table(name="person", indexes={@ORM\Index(name="IDX_PERSON_LASTNAME", columns={"lastname"}), @ORM\Index(name="IDX_PERSON_FIRSTNAME", columns={"firstname"}), @ORM\Index(name="IDX_PERSON_PHONE", columns={"mobilephone"}), @ORM\Index(name="sprite_id", columns={"sprite_id"})})
 * @ORM\Entity
 */
class Person
{
    use PropertyAccessorTrait;

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
     * @ORM\Column(name="department", type="string", length=64, nullable=true)
     */
    private $department;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=32, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="jobtitle", type="string", length=64, nullable=true)
     */
    private $jobtitle;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="mobilephone", type="string", length=24, nullable=true)
     */
    private $mobilephone;

    /**
     * @var string
     *
     * @ORM\Column(name="officephone", type="string", length=24, nullable=true)
     */
    private $officephone;

    /**
     * @var string
     *
     * @ORM\Column(name="officefax", type="string", length=24, nullable=true)
     */
    private $officefax;

    /**
     * @var integer
     *
     * @ORM\Column(name="ownedsecurableitem_id", type="integer", nullable=true)
     */
    private $ownedsecurableitemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="title_ownedcustomfield_id", type="integer", nullable=true)
     */
    private $titleOwnedcustomfieldId;

    /**
     * @var integer
     *
     * @ORM\Column(name="primaryemail_email_id", type="integer", nullable=true)
     */
    private $primaryemailEmailId;

    /**
     * @var integer
     *
     * @ORM\Column(name="primaryaddress_address_id", type="integer", nullable=true)
     */
    private $primaryaddressAddressId;

    /**
     * @var integer
     *
     * @ORM\Column(name="title_customfield_id", type="integer", nullable=true)
     */
    private $titleCustomfieldId;

    /**
     * @var integer
     *
     * @ORM\Column(name="sprite_id", type="integer", nullable=true)
     */
    private $spriteId;

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->lastname, $this->firstname);
    }
}

