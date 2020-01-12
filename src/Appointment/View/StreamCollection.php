<?php

namespace App\Appointment\View;

use App\Appointment\Entity\Appointment;
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
     * @param Appointment[] $appointments
     */
    public function __construct(array $appointments)
    {
        foreach ($appointments as $appointment) {
            $this->add($appointment);
        }
    }

    public function add(Appointment $appointment): void
    {
        $worker = null !== $appointment->order ? $appointment->order->getWorker() : null;
        if (null !== $worker) {
            foreach ($this->streams as $stream) {
                if ($stream->worker === $worker) {
                    try {
                        $stream->add($appointment);

                        return;
                    } catch (StreamOverflowException $e) {
                        $this->streams[] = new Stream($worker, [$appointment]);

                        return;
                    }
                }
            }

            $this->streams[] = new Stream($worker, [$appointment]);

            return;
        }

        foreach ($this->streams as $stream) {
            try {
                $stream->add($appointment);

                return;
            } catch (StreamOverflowException $e) {
            }
        }

        $this->streams[] = new Stream(null, [$appointment]);
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
