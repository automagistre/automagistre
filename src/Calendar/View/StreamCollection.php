<?php

namespace App\Calendar\View;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
use function usort;

/**
 * @implements IteratorAggregate<Stream>
 */
final class StreamCollection implements IteratorAggregate
{
    /**
     * @var array<int, Stream>
     */
    private array $streams = [];

    /**
     * @param CalendarEntryView[] $calendars
     */
    public function __construct(array $calendars)
    {
        foreach ($calendars as $calendar) {
            $this->add($calendar);
        }
    }

    public function add(CalendarEntryView $entry): void
    {
        $worker = $entry->worker;
        if (null !== $worker) {
            foreach ($this->streams as $stream) {
                if ($stream->worker === $worker) {
                    try {
                        $stream->add($entry);

                        return;
                    } catch (StreamOverflowException $e) {
                        $this->streams[] = new Stream($worker, [$entry]);

                        return;
                    }
                }
            }

            $this->streams[] = new Stream($worker, [$entry]);

            return;
        }

        foreach ($this->streams as $stream) {
            try {
                $stream->add($entry);

                return;
            } catch (StreamOverflowException $e) {
            }
        }

        $this->streams[] = new Stream(null, [$entry]);
    }

    /**
     * @return Traversable<int, Stream>
     */
    public function getIterator(): Traversable
    {
        usort($this->streams, static function (Stream $left, Stream $right): int {
            if (null === $left->worker && null === $right->worker) {
                return 0;
            }

            if (null !== $left->worker && null === $right->worker) {
                return -1;
            }

            if (null === $left->worker) {
                return 1;
            }

            return $left->worker->getFullName() <=> $right->worker->getFullName();
        });

        return new ArrayIterator($this->streams);
    }
}
