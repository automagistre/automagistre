<?php

namespace App\Calendar\View;

use App\Calendar\Form\CalendarEntryDto;
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
     * @param CalendarEntryDto[] $calendars
     */
    public function __construct(array $calendars)
    {
        foreach ($calendars as $calendar) {
            $this->add($calendar);
        }
    }

    public function add(CalendarEntryDto $entry): void
    {
        $worker = $entry->orderInfo->workerId;
        if (null !== $worker) {
            foreach ($this->streams as $stream) {
                if ($stream->workerId === $worker) {
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
            if (null === $left->workerId && null === $right->workerId) {
                return 0;
            }

            if (null !== $left->workerId && null === $right->workerId) {
                return -1;
            }

            if (null === $left->workerId) {
                return 1;
            }

            return $left->workerId->toString() <=> $right->workerId->toString();
        });

        return new ArrayIterator($this->streams);
    }
}
