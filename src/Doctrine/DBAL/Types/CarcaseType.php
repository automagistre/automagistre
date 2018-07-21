<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use App\Enum\Carcase;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarcaseType extends EnumType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'carcase_enum';
    }

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Carcase::class;
    }
}
