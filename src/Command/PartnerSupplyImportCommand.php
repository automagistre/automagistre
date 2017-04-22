<?php

declare(strict_types=1);

namespace App\Command;

use App\Partner\Ixora\Orders;
use App\Supply\Importer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartnerSupplyImportCommand extends Command
{
    /**
     * @var Orders
     */
    private $order;

    /**
     * @var Importer
     */
    private $importer;

    public function __construct(Orders $order, Importer $importer)
    {
        parent::__construct();

        $this->order = $order;
        $this->importer = $importer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('partner:orders:import');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->importer->import($this->order);
    }
}
