<?php

declare(strict_types=1);

namespace App\Part\View;

use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Shared\Doctrine\Registry;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use function str_replace;

final class PartFormatter implements IdentifierFormatterInterface
{
    private const FORMATS = [
        null => ':manufacturer: - :name: (:number:)',
        'name' => ':name:',
        'number' => ':number:',
        'autocomplete' => ':number: - :manufacturer: (:name:)',
        'manufacturer' => ':manufacturer:',
    ];

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $part = $this->registry->get(Part::class, $identifier);

        return str_replace(
            [
                ':manufacturer:',
                ':name:',
                ':number:',
            ],
            [
                $formatter->format($part->manufacturerId),
                $part->name,
                $part->number->number,
            ],
            self::FORMATS[$format],
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return PartId::class;
    }
}
