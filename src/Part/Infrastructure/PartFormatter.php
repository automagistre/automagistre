<?php

declare(strict_types=1);

namespace App\Part\Infrastructure;

use App\Doctrine\ORM\Type\Identifier;
use App\Doctrine\Registry;
use App\Part\Domain\Part;
use App\Part\Domain\PartId;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use Doctrine\ORM\AbstractQuery;
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
        $view = $this->registry->repository(Part::class)
            ->createQueryBuilder('t')
            ->where('t.partId = :id')
            ->setParameter('id', $identifier)
            ->getQuery()
            ->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        return str_replace(
            [
                ':manufacturer:',
                ':name:',
                ':number:',
            ],
            [
                $formatter->format($view['manufacturerId']),
                $view['name'],
                $view['number'],
            ],
            self::FORMATS[$format]
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
