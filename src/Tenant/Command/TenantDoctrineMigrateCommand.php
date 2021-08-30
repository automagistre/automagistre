<?php

declare(strict_types=1);

namespace App\Tenant\Command;

use App\Tenant\Enum\Tenant;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class TenantDoctrineMigrateCommand extends Command
{
    protected static $defaultName = 'tenant:doctrine:migrate';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $processes = [];

        foreach (Tenant::all() as $tenant) {
            $process = new Process(
                [
                    'console',
                    'doctrine:migrations:migrate',
                    '--no-interaction',
                    '--allow-no-migration',
                    '--all-or-nothing=true',
                ],
                null,
                [
                    'TENANT' => $tenant->toIdentifier(),
                ],
            );

            $process->enableOutput()->start();
            $processes[] = $process;
        }

        foreach ($processes as $process) {
            $process->wait();

            $output->writeln('======== '.$process->getEnv()['TENANT'].' ======== ');
            $output->write($process->getErrorOutput());
            $output->write($process->getOutput());
        }

        return self::SUCCESS;
    }
}
