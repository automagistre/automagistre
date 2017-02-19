<?php

namespace Tests\AppBundle\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class DefaultControllerTest extends TestCase
{
    public function testIndex()
    {
        $kernel = new \AppKernel('test', 0);

        self::assertSame(200, $kernel->handle(Request::create('/'))->getStatusCode());
    }
}
