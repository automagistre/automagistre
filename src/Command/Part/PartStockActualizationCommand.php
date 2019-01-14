<?php

declare(strict_types=1);

namespace App\Command\Part;

use App\Doctrine\Registry;
use App\Entity\Tenant\Motion;
use App\Manager\StockpileManager;
use App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartStockActualizationCommand extends Command
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var StockpileManager
     */
    private $stockpileManager;

    /**
     * @var State
     */
    private $state;

    public function __construct(Registry $registry, StockpileManager $stockpileManager, State $state)
    {
        parent::__construct('part:stock:actualize');

        $this->registry = $registry;
        $this->stockpileManager = $stockpileManager;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tenant = $this->state->tenant();

        $values = $this->registry->repository(Motion::class)->createQueryBuilder('entity')
            ->select('entity.part.id AS part_id, SUM(entity.quantity) AS quantity')
            ->groupBy('entity.part.id')
            ->having('SUM(entity.quantity) > 0')
            ->getQuery()
            ->getArrayResult();

        $count = \count($values);

        $values = \array_map(function (array $item) use ($tenant) {
            return [$item['part_id'], $tenant, $item['quantity']];
        }, $values);

        $this->stockpileManager->actualize($values);

        $io->success(\sprintf('Updated %s rows', $count));

        return 0;
    }
}
