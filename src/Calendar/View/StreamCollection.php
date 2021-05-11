<?php

declare(strict_types=1);

namespace App\Calendar\View;

use App\Calendar\Entity\EntryView;
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
     * @param EntryView[] $calendars
     */
    public function __construct(array $calendars)
    {
        foreach ($calendars as $calendar) {
            $this->add($calendar);
        }
    }

    public function add(EntryView $entry): void
    {
        $workerId = $entry->orderInfo->workerId;

        if (null !== $workerId) {
            foreach ($this->streams as $stream) {
                if ($workerId->equals($stream->workerId)) {
                    try {
                        $stream->add($entry);

                        return;
                    } catch (StreamOverflowException) {
                        $this->streams[] = new Stream($workerId, [$entry]);

                        return;
                    }
                }
            }

            $this->streams[] = new Stream($workerId, [$entry]);

            return;
        }

        foreach ($this->streams as $stream) {
            try {
                $stream->add($entry);

                return;
            } catch (StreamOverflowException) {
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
