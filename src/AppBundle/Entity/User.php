<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User.
 *
 * @ORM\Table(name="_user", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_2f9f20ae60de87f7bdd974b52941c30e287c6eef", columns={"username"})})
 * @ORM\Entity
 */
class User
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
     * @ORM\Column(name="person_id", type="integer", nullable=true)
     */
    private $personId;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=32, nullable=true)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=10, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=64, nullable=true)
     */
    private $timezone;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=64, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="serializedavatardata", type="text", length=65535, nullable=true)
     */
    private $serializedavatardata;

    /**
     * @var int
     *
     * @ORM\Column(name="permitable_id", type="integer", nullable=true)
     */
    private $permitableId;

    /**
     * @var bool
     *
     * @ORM\Column(name="manager__user_id", type="boolean", nullable=true)
     */
    private $managerUserId;

    /**
     * @var bool
     *
     * @ORM\Column(name="role_id", type="boolean", nullable=true)
     */
    private $roleId;

    /**
     * @var bool
     *
     * @ORM\Column(name="currency_id", type="boolean", nullable=true)
     */
    private $currencyId;

    /**
     * @var bool
     *
     * @ORM\Column(name="isactive", type="boolean", nullable=true)
     */
    private $isactive;
}
