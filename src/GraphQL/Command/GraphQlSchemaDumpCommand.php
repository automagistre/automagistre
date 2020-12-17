<?php

declare(strict_types=1);

namespace App\GraphQL\Command;

use App\GraphQL\Www;
use function file_put_contents;
use GraphQL\Utils\SchemaPrinter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GraphQlSchemaDumpCommand extends Command
{
    protected static $defaultName = 'graphql:schema:dump';

    protected function configure(): void
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'Path to dump');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $path */
        $path = $input->getArgument('path');

        $schema = Www\Schema::create();

        file_put_contents($path.'/www.graphql', SchemaPrinter::doPrint($schema));

        return 0;
    }
}
