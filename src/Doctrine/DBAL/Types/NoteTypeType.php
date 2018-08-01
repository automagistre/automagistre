<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use App\Enum\NoteType;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NoteTypeType extends EnumType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'note_type_enum';
    }

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return NoteType::class;
    }
}
