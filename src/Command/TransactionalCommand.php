<?php

declare(strict_types=1);

namespace App\Command;

use App\Shared\Doctrine\Registry;
use Doctrine\ORM\EntityManagerInterface;
use MichaelPetri\TypedInput\TypedInput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @mixin Command
 */
trait TransactionalCommand
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private Registry $registry;

    #[Required]
    public function setRegistry(Registry $registry): void
    {
        $this->registry = $registry;
    }

    abstract protected function transactional(TypedInput $input, SymfonyStyle $io, EntityManagerInterface $em): int;

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $callable = function (EntityManagerInterface $em) use ($input, $output): int {
            return $this->transactional(TypedInput::fromInput($input), new SymfonyStyle($input, $output), $em);
        };

        $exitCode = $this->registry->manager()->transactional($callable);

        if (true === $exitCode) {
            return self::SUCCESS;
        }

        return $exitCode;
    }
}
