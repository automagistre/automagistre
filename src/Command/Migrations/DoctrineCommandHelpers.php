<?php

declare(strict_types=1);

namespace App\Command\Migrations;

use Doctrine\Bundle\MigrationsBundle\Command\Helper\DoctrineCommandHelper;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Pass em and db Helpers.
 *
 * Doctrine commands not allow pass both db and em options, don't know why
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 *
 * @mixin Command
 */
trait DoctrineCommandHelpers
{
    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $application = $this->getApplication();
        if (!$application instanceof Application) {
            throw new LogicException(
                \sprintf('"%s" expected, "%s" given', Application::class, \get_class($application))
            );
        }

        /** @var string $db */
        $db = $input->getOption('db');
        DoctrineCommandHelper::setApplicationConnection($application, $db);

        /** @var string $em */
        $em = $input->getOption('em');
        DoctrineCommandHelper::setApplicationEntityManager($application, $em);

        $input->setOption('db', null);

        return parent::execute($input, $output);
    }
}
