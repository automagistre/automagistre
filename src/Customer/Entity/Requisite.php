<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use function implode;

/**
 * @ORM\Embeddable
 */
final class Requisite
{
    /**
     * @var null|string
     *
     * @ORM\Column(nullable=true)
     */
    public $bank;

    /**
     * @var null|string
     *
     * @ORM\Column(nullable=true)
     */
    public $legalAddress;

    /**
     * @var null|string
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     *
     * @ORM\Column(nullable=true)
     */
    public $ogrn;

    /**
     * @var null|string
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     *
     * @ORM\Column(nullable=true)
     */
    public $inn;

    /**
     * @var null|string
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     *
     * @ORM\Column(nullable=true)
     */
    public $kpp;

    /**
     * @var null|string
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     * @Assert\Length(min="20", max="20")
     *
     * @ORM\Column(nullable=true)
     */
    public $rs;

    /**
     * @var null|string
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     * @Assert\Length(min="20", max="20")
     *
     * @ORM\Column(nullable=true)
     */
    public $ks;

    /**
     * @var null|string
     *
     * @Assert\Type("numeric", message="Значение может содержать только цифры.")
     *
     * @ORM\Column(nullable=true)
     */
    public $bik;

    public function __toString(): string
    {
        $data = [];

        if (null !== $this->ogrn) {
            $data[] = 'ОГРН: '.$this->ogrn;
        }

        if (null !== $this->inn) {
            $data[] = 'ИНН: '.$this->inn;
        }

        if (null !== $this->kpp) {
            $data[] = 'КПП: '.$this->kpp;
        }

        if (null !== $this->rs) {
            $data[] = 'Р/С: '.$this->rs;
        }

        if (null !== $this->ks) {
            $data[] = 'К/С: '.$this->ks;
        }

        if (null !== $this->bik) {
            $data[] = 'БИК: '.$this->bik;
        }

        return implode(', ', $data);
    }

    public function isEmpty(): bool
    {
        return null === $this->ogrn
            && null === $this->inn
            && null === $this->kpp
            && null === $this->rs
            && null === $this->ks
            && null === $this->bik;
    }
}
