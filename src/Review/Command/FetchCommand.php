<?php

declare(strict_types=1);

namespace App\Review\Command;

use App\Review\Entity\ReviewId;
use App\Review\Fetch\Fetcher;
use App\Shared\Doctrine\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FetchCommand extends Command
{
    protected static $defaultName = 'review:fetch';

    private Registry $registry;

    private Fetcher $fetcher;

    public function __construct(Registry $registry, Fetcher $fetcher)
    {
        parent::__construct();

        $this->registry = $registry;
        $this->fetcher = $fetcher;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->fetcher->fetch() as $review) {
            $this->registry->add($review->toReview(ReviewId::generate()));
        }

        return 0;
    }
}
