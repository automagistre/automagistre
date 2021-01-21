<?php

declare(strict_types=1);

namespace App\Review\Fetch;

use App\Shared\Doctrine\Registry;
use Psr\Log\LoggerInterface;

final class FilterFetcher implements Fetcher
{
    private Registry $registry;

    private Fetcher $fetcher;

    private LoggerInterface $logger;

    public function __construct(Registry $registry, Fetcher $fetcher, LoggerInterface $logger)
    {
        $this->registry = $registry;
        $this->fetcher = $fetcher;
        $this->logger = $logger;
    }

    public function fetch(): iterable
    {
        foreach ($this->fetcher->fetch() as $review) {
            if ($this->isExists($review)) {
                $this->logger->debug('Review already exists, skipped.', [
                    'id' => $review->sourceId,
                    'source' => $review->source->toDisplayName(),
                ]);

                continue;
            }

            $this->logger->info('New review received.', [
                'id' => $review->sourceId,
                'source' => $review->source->toDisplayName(),
            ]);

            yield $review;
        }
    }

    private function isExists(FetchedReview $review): bool
    {
        $conn = $this->registry->connection();

        $exists = $conn->fetchOne('SELECT 1 FROM review WHERE source = :source AND source_id = :sourceId', [
            'source' => $review->source,
            'sourceId' => $review->sourceId,
        ]);

        return 1 === $exists;
    }
}
