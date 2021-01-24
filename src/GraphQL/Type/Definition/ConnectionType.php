<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use App\GraphQL\Type\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use ReflectionClass;
use function strrpos;
use function substr_replace;

final class ConnectionType extends ObjectType
{
    public function __construct(Type $type)
    {
        $config = [
            'name' => self::inferName((new ReflectionClass($type))->getShortName()),
            'fields' => fn (): array => [
                'nodes' => [
                    'type' => Types::listOf($type),
                ],
                'pageInfo' => [
                    'type' => Types::nonNull(Types::pageInfo()),
                ],
                'totalCount' => [
                    'type' => Types::nonNull(Types::int()),
                ],
            ],
        ];

        parent::__construct($config);
    }

    public static function inferName(string $name): string
    {
        $pos = strrpos($name, 'Type');

        if (false !== $pos) {
            $name = substr_replace($name, 'Connection', $pos, 10);
        }

        return $name;
    }
}
