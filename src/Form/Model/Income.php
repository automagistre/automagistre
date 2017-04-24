<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Operand;
use App\Entity\Part;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Income extends Model
{
    /**
     * @var Operand
     *
     * @Assert\NotBlank()
     */
    public $supplier;

    /**
     * @var Part[]
     *
     * @Assert\NotBlank(message="Поступление не может быть без позиций")
     */
    public $parts;

    public static function getEntityClass(): string
    {
        return \App\Entity\Income::class;
    }
}
