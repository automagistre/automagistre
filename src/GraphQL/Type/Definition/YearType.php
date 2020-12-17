<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use GraphQL\Type\Definition\IntType;

final class YearType extends IntType
{
    public $name = 'year';
    public $description = '';
}
