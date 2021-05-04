<?php

declare(strict_types=1);

namespace App\CreatedBy\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Exclude
{
}
