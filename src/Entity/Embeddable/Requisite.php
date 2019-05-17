<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
final class Requisite
{
    /**
     * @var string|null
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     *
     * @ORM\Column(nullable=true)
     */
    public $ogrn;

    /**
     * @var string|null
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     *
     * @ORM\Column(nullable=true)
     */
    public $inn;

    /**
     * @var string|null
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     *
     * @ORM\Column(nullable=true)
     */
    public $kpp;

    /**
     * @var string|null
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     * @Assert\Length(min="20", max="20")
     *
     * @ORM\Column(nullable=true)
     */
    public $rs;

    /**
     * @var string|null
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     * @Assert\Length(min="20", max="20")
     *
     * @ORM\Column(nullable=true)
     */
    public $ks;

    /**
     * @var string|null
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     *
     * @ORM\Column(nullable=true)
     */
    public $bik;
}
