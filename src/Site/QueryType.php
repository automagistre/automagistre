<?php

declare(strict_types=1);

namespace App\Site;

use App\GraphQL\Type\Connection;
use App\GraphQL\Type\PageInfo;
use App\GraphQL\Type\Types;
use App\MC\Entity\McEquipment;
use App\Publish\Entity\PublishView;
use App\Review\Entity\Review;
use App\Vehicle\Entity\Model;
use function array_pop;
use function base64_decode;
use function base64_encode;
use function count;
use const DATE_RFC3339;
use DateTimeImmutable;
use Doctrine\ORM\Query\Expr\Join;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

final class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'reviews' => [
                    'type' => fn (): Type => Types::connection(Types::review()),
                    'args' => [
                        'first' => [
                            'type' => Types::int(),
                            'defaultValue' => 10,
                        ],
                        'after' => [
                            'type' => Types::string(),
                        ],
                    ],
                    'resolve' => function ($rootValue, array $args, Context $context): Connection {
                        $after = $args['after'] ?? null;
                        $first = (int) $args['first'];

                        $qb = $context->registry->manager()->createQueryBuilder()
                            ->select('t')
                            ->from(Review::class, 't')
                            ->where('t.text <> \'\'');

                        $totalCount = (int) (clone $qb)->select('COUNT(t)')->getQuery()->getSingleScalarResult();

                        $qb->orderBy('t.publishAt', 'DESC');

                        if (null !== $after) {
                            $publishAtDecoded = base64_decode($after, true);
                            if (false === $publishAtDecoded) {
                                throw new Error('Invalid after arg.');
                            }

                            $publishAt = DateTimeImmutable::createFromFormat(DATE_RFC3339, $publishAtDecoded);
                            if (false === $publishAt) {
                                throw new Error('Invalid after arg.');
                            }

                            $qb
                                ->andWhere('t.publishAt < :publishAt')
                                ->setParameter('publishAt', $publishAt);
                        }

                        $nodes = $qb
                            ->getQuery()
                            ->setMaxResults($first + 1)
                            ->getResult();

                        $endCursor = null;
                        $hasNextPage = count($nodes) > $first;
                        if ($hasNextPage) {
                            /** @var Review $nextNode */
                            $nextNode = array_pop($nodes);
                            $endCursor = base64_encode($nextNode->publishAt->format(DATE_RFC3339));
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
            ],
        ];

        parent::__construct($config);
    }
}
