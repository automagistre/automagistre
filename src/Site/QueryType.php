<?php

declare(strict_types=1);

namespace App\Site;

use App\GraphQL\Type\Connection;
use App\GraphQL\Type\PageInfo;
use App\GraphQL\Type\Types;
use App\MC\Entity\McEquipment;
use App\MC\Entity\McLine;
use App\Part\Entity\PartView;
use App\Publish\Entity\PublishView;
use App\Review\Entity\ReviewView;
use App\Vehicle\Entity\Model;
use function array_pop;
use function count;
use Doctrine\ORM\Query\Expr\Join;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

final class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'part' => [
                    'type' => fn (): Type => Types::part(),
                    'args' => [
                        'id' => Types::nonNull(Types::uuid()),
                    ],
                    'resolve' => function ($rootValue, array $args, Context $context): PartView {
                        return $context->registry->get(PartView::class, $args['id']);
                    },
                ],
                'reviews' => [
                    'type' => fn (): Type => Types::connection(Types::review()),
                    'args' => [
                        'first' => [
                            'type' => Types::int(),
                            'defaultValue' => 10,
                        ],
                        'after' => [
                            'type' => Types::uuid(),
                        ],
                    ],
                    'resolve' => function ($rootValue, array $args, Context $context): Connection {
                        $after = $args['after'] ?? null;
                        $first = (int) $args['first'];

                        $qb = $context->registry->manager()->createQueryBuilder()
                            ->select('t')
                            ->from(ReviewView::class, 't');

                        $totalCount = (int) (clone $qb)->select('COUNT(t)')->getQuery()->getSingleScalarResult();

                        $qb->orderBy('t.id', 'DESC');

                        if (null !== $after) {
                            $qb
                                ->where('t.id < :id')
                                ->setParameter('id', $after);
                        }

                        $nodes = $qb
                            ->getQuery()
                            ->setMaxResults($first + 1)
                            ->getResult();

                        $endCursor = null;
                        $hasNextPage = count($nodes) > $first;
                        if ($hasNextPage) {
                            /** @var ReviewView $nextNode */
                            $nextNode = array_pop($nodes);
                            $endCursor = $nextNode->toId()->toString();
                        }

                        return new Connection(
                            $nodes,
                            new PageInfo(
                                $hasNextPage,
                                false,
                                $endCursor,
                                null,
                            ),
                            $totalCount,
                        );
                    },
                ],
                'vehicle' => [
                    'type' => fn (): Type => Types::nonNull(Types::vehicle()),
                    'args' => [
                        'id' => [
                            'type' => Types::nonNull(Types::uuid()),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): Model {
                        return $context->registry->get(Model::class, $args['id']);
                    },
                ],
                'vehicles' => [
                    'type' => fn (): Type => Types::listOf(Types::vehicle()),
                    'args' => [
                        'manufacturerId' => [
                            'type' => Types::nonNull(Types::uuid()),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): array {
                        return $context->registry->manager()
                            ->createQueryBuilder()
                            ->select('t')
                            ->from(Model::class, 't')
                            ->join(McEquipment::class, 'mc', Join::WITH, 'mc.vehicleId = t.id')
                            ->join(PublishView::class, 'p', Join::WITH, 'p.id = mc.id AND p.published = TRUE')
                            ->where('t.manufacturerId = :manufacturerId')
                            ->getQuery()
                            ->setParameter('manufacturerId', $args['manufacturerId'])
                            ->getResult();
                    },
                ],
                'maintenances' => [
                    'type' => fn (): Type => Types::listOf(Types::maintenance()),
                    'args' => [
                        'vehicleId' => [
                            'type' => Types::nonNull(Types::uuid()),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): array {
                        return $context->registry->manager()
                            ->createQueryBuilder()
                            ->select('t')
                            ->from(McEquipment::class, 't')
                            ->join(PublishView::class, 'publish', Join::WITH, 'publish.id = t.id AND publish.published = TRUE')
                            ->where('t.vehicleId = :vehicleId')
                            ->getQuery()
                            ->setParameter('vehicleId', $args['vehicleId'])
                            ->getResult();
                    },
                ],
                'works' => [
                    'type' => fn (): Type => Types::listOf(Types::work()),
                    'args' => [
                        'maintenanceId' => [
                            'type' => Types::nonNull(Types::uuid()),
                        ],
                    ],
                    'resolve' => static function ($rootValue, array $args, Context $context): array {
                        return $context->registry->manager()
                            ->createQueryBuilder()
                            ->select('t')
                            ->from(McLine::class, 't')
                            ->where('t.equipment = :equipment')
                            ->getQuery()
                            ->setParameter('equipment', $args['maintenanceId'])
                            ->getResult();
                    },
                ],
            ],
        ];

        parent::__construct($config);
    }
}
