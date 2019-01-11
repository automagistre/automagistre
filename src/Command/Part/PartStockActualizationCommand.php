<?php

declare(strict_types=1);

namespace App\Command\Part;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\Motion;
use App\Enum\Tenant;
use App\Manager\StockpileManager;
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

    public function __construct(Registry $registry, StockpileManager $stockpileManager)
    {
        parent::__construct('part:stock:actualize');

        $this->registry = $registry;
        $this->stockpileManager = $stockpileManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $tenantIdentifier */
        $tenantIdentifier = $input->getOption('tenant');
        $tenant = Tenant::fromIdentifier($tenantIdentifier);

        $values = $this->registry->repository(Motion::class)->createQueryBuilder('entity')
            ->select('entity.part.uuid AS uuid, SUM(entity.quantity) AS quantity')
            ->groupBy('entity.part.uuid')
            ->having('SUM(entity.quantity) > 0')
            ->getQuery()
            ->getArrayResult();

        $count = \count($values);

        $values = \array_map(function (array $item) use ($tenant) {
            $partId = $this->registry->repository(Part::class)->createQueryBuilder('entity')
                ->select('entity.id')
                ->where('entity.uuid = :uuid')
                ->setParameter('uuid', $item['uuid'], 'uuid_binary')
                ->getQuery()
                ->useQueryCache(true)
                ->useResultCache(true)
                ->getSingleScalarResult();

            return [$partId, $tenant, $item['quantity']];
        }, $values);

        $this->stockpileManager->actualize($values);

        $io->success(\sprintf('Updated %s rows', $count));

        return 0;
    }
}
