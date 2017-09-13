<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class DefaultControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $kernel = new Kernel('test', false);

        self::assertSame(200, $kernel->handle(Request::create('/'))->getStatusCode());
    }
}
