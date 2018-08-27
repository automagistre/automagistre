<?php

declare(strict_types=1);

namespace App\Command;

use App\Supply\Importer;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartnerSupplyImportCommand extends Command
{
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @var Importer
     */
    private $importer;

    public function __construct(Importer $importer)
    {
        parent::__construct();

        $this->importer = $importer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('partner:orders:import')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, '', (new DateTime())->format(self::DATE_FORMAT));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $string = $input->getOption('date');
        $object = DateTime::createFromFormat(self::DATE_FORMAT, $string);
        if (!$object instanceof DateTime) {
            throw new InvalidArgumentException(
                \sprintf('Can\'t create "%s" from string "%s"', DateTime::class, $string)
            );
        }

        $this->importer->import($object);
    }
}
