<?php

declare(strict_types=1);

namespace App\Tests\Site;

use App\Site\Schema;
use PHPUnit\Framework\TestCase;

final class SchemaAssertTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function test(): void
    {
        Schema::create()->assertValid();
    }
}
