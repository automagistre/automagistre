<?php

declare(strict_types=1);

namespace App\Foo\Command;

use App\Foo\Entity\Foo;
use App\Shared\Doctrine\Registry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FooCommand extends Command
{
    protected static $defaultName = 'foo:test';

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        parent::__construct();

        $this->registry = $registry;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bar = new Foo();
        $bar->id = Uuid::uuid6();

        $this->registry->add($bar);

        return 0;
    }
}
