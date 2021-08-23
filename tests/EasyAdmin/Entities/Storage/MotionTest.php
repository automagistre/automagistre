<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Entities\Storage;

use App\Fixtures\Part\GasketFixture;
use App\Tests\EasyAdminTestCase;

final class MotionTest extends EasyAdminTestCase
{
    /**
     * @see \App\Storage\Controller\MotionController::listAction()
     */
    public function testList(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'action' => 'list',
            'entity' => 'Motion',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\MotionController::increaseAction()
     */
    public function testIncrease(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'part_id' => GasketFixture::ID,
            'action' => 'increase',
            'entity' => 'Motion',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\MotionController::decreaseAction()
     */
    public function testDecrease(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'part_id' => GasketFixture::ID,
            'action' => 'decrease',
            'entity' => 'Motion',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @see \App\Storage\Controller\MotionController::actualizeAction()
     */
    public function testActualize(): void
    {
        $client = self::createClient();

        $client->request('GET', '/msk/?'.http_build_query([
            'part_id' => GasketFixture::ID,
            'action' => 'actualize',
            'entity' => 'Motion',
        ]));

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
