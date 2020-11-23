<?php

declare(strict_types=1);

namespace App\Review\Command;

use App\Review\Document\Review;
use App\Shared\Doctrine\Registry;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MongoReviewPopulateCommand extends Command
{
    protected static $defaultName = 'mongo:review:populate';

    private Registry $registry;

    private ManagerRegistry $odmRegistry;

    public function __construct(Registry $registry, ManagerRegistry $odmRegistry)
    {
        parent::__construct(null);

        $this->registry = $registry;
        $this->odmRegistry = $odmRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dm = $this->odmRegistry->getManager('www');

        $qb = $this->registry->manager()->createQueryBuilder()
            ->select('t')
            ->from(Review::class, 't');

        foreach ($qb->getQuery()->iterate() as [$item]) {
            $dm->persist($item);
        }

        $dm->flush();

        return 0;
    }
}
