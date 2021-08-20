<?php

declare(strict_types=1);

namespace App\Review\Command;

use App\Doctrine\Registry;
use App\Review\Entity\ReviewId;
use App\Review\Fetch\Fetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FetchCommand extends Command
{
    protected static $defaultName = 'review:fetch';

    public function __construct(private Registry $registry, private Fetcher $fetcher)
    {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->fetcher->fetch() as $review) {
            $this->registry->add($review->toReview(ReviewId::generate()));
        }

        $this->registry->flush();

        return 0;
    }
}
