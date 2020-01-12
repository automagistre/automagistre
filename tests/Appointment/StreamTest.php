<?php

namespace App\Tests\Appointment;

use App\Appointment\Entity\Appointment;
use App\Appointment\View\Stream;
use App\Appointment\View\StreamOverflowException;
use DateInterval;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    /**
     * @param Appointment[] $entities
     * @param Appointment[] $items
     *
     * @dataProvider validData
     */
    public function testHappyPath(array $entities, array $items): void
    {
        $stream = new Stream(null, $entities);

        foreach ($items as $key => $entity) {
            static::assertTrue($stream->has($key));
            static::assertSame($entity, $stream->get($key)->appointment);
        }
    }

    public function validData(): Generator
    {
        $entities = [];
        $items = [];

        $entity = $entities[] = $items['10:30'] = new Appointment();
        $entity->date = new DateTimeImmutable('10:30');
        $entity->duration = new DateInterval('PT30M');

        $entity = $entities[] = $items['11:00'] = new Appointment();
        $entity->date = new DateTimeImmutable('11:00');
        $entity->duration = new DateInterval('PT1H');

        $entity = $entities[] = $items['12:00'] = new Appointment();
        $entity->date = new DateTimeImmutable('12:00');
        $entity->duration = new DateInterval('PT2H30M');

        $entity = $entities[] = $items['15:00'] = new Appointment();
        $entity->date = new DateTimeImmutable('15:00');
        $entity->duration = new DateInterval('PT30M');

        yield [$entities, $items];
    }

    /**
     * @param Appointment[] $entities
     *
     * @dataProvider overflowData
     */
    public function testOverflow(array $entities): void
    {
        $this->expectException(StreamOverflowException::class);

        new Stream(null, $entities);
    }

    public function overflowData(): Generator
    {
        $entities = [];

        $entity = $entities[] = new Appointment();
        $entity->date = new DateTimeImmutable('10:30');
        $entity->duration = new DateInterval('PT1H');

        $entity = $entities[] = new Appointment();
        $entity->date = new DateTimeImmutable('11:00');
        $entity->duration = new DateInterval('PT1H');

        yield [$entities];
    }
}
