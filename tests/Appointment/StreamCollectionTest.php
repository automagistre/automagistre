<?php

namespace App\Tests\Appointment;

use App\Appointment\Entity\Appointment;
use App\Appointment\View\Stream;
use App\Appointment\View\StreamCollection;
use App\Entity\Landlord\Person;
use App\Entity\Tenant\Employee;
use App\Entity\Tenant\Order;
use function count;
use DateInterval;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;

class StreamCollectionTest extends TestCase
{
    /**
     * @param Appointment[] $entities
     * @param Stream[] $expected
     *
     * @dataProvider validData
     */
    public function testHappyPath(array $entities, array $expected): void
    {
        /** @var Stream[] $collection */
        $collection = new StreamCollection($entities);

        static::assertCount(count($expected), $collection);

        foreach ($collection as $key => $value) {
            static::assertSame($expected[$key]->worker, $value->worker);

            $items = $expected[$key]->all();
            foreach ($value->all() as $key2 => $item) {
                static::assertSame($items[$key2]->appointment, $item->appointment);
                static::assertSame($items[$key2]->length, $item->length);
            }
        }
    }

    public function validData(): Generator
    {
        $entities = [];

        $worker1 = new Employee();
        $worker1->setPerson($person = new Person());
        $person->setFirstname('Vasya');
        $person->setLastname('Pupkin');
        $order1 = new Order();
        $order1->setWorker($worker1);

        $worker2 = new Employee();
        $worker2->setPerson($person = new Person());
        $person->setFirstname('Petr');
        $person->setLastname('Pushkin');
        $order2 = new Order();
        $order2->setWorker($worker2);

        $entity1 = $entities[] = new Appointment();
        $entity1->order = $order1;
        $entity1->date = new DateTimeImmutable('10:30');
        $entity1->duration = new DateInterval('PT1H');

        $entity2 = $entities[] = new Appointment();
        $entity2->order = $order1;
        $entity2->date = new DateTimeImmutable('11:00');
        $entity2->duration = new DateInterval('PT8H');

        $entity3 = $entities[] = new Appointment();
        $entity3->order = $order2;
        $entity3->date = new DateTimeImmutable('12:00');
        $entity3->duration = new DateInterval('PT5H30M');

        $entity4 = $entities[] = new Appointment();
        $entity4->date = new DateTimeImmutable('15:00');
        $entity4->duration = new DateInterval('PT30M');

        $entity5 = $entities[] = new Appointment();
        $entity5->date = new DateTimeImmutable('15:00');
        $entity5->duration = new DateInterval('PT30M');

        $result = [
            new Stream($worker1, [$entity1, $entity4]),
            new Stream($worker1, [$entity2]),
            new Stream($worker2, [$entity3]),
            new Stream(null, [$entity5]),
        ];

        yield [$entities, $result];
    }
}
