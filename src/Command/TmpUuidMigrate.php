<?php

declare(strict_types=1);

namespace App\Command;

use App\Doctrine\Registry;
use App\Entity\Tenant\Employee;
use App\Entity\Tenant\ExpenseItem;
use App\Entity\Tenant\Income;
use App\Entity\Tenant\IncomePart;
use App\Entity\Tenant\Motion;
use App\Entity\Tenant\MotionManual;
use App\Entity\Tenant\OperandTransaction;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItem;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\OrderItemService;
use App\Entity\Tenant\OrderNote;
use App\Entity\Tenant\OrderPayment;
use App\Entity\Tenant\OrderSuspend;
use App\Entity\Tenant\WalletTransaction;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TmpUuidMigrate extends Command
{
    private const MAP = [
        Order::class => ['createdBy', 'car', 'customer', 'closedBy'],
        OrderItem::class => ['createdBy'],
        OrderItemService::class => ['worker'],
        OrderItemPart::class => ['part'],
        Income::class => ['supplier', 'accruedBy', 'createdBy'],
        IncomePart::class => ['part'],
        Employee::class => ['person'],
        OrderPayment::class => ['createdBy'],
        Motion::class => ['part'],
        OrderNote::class => ['createdBy'],
        ExpenseItem::class => ['createdBy'],
        MotionManual::class => ['user'],
        WalletTransaction::class => ['createdBy'],
        OperandTransaction::class => ['recipient', 'createdBy'],
        OrderSuspend::class => ['createdBy'],
    ];
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        parent::__construct('temp:uuid:migrate');

        $this->registry = $registry;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach (self::MAP as $class => $fields) {
            $this->populate($class, $fields, $io);
        }

        return 0;
    }

    private function populate(string $class, array $fields, SymfonyStyle $io): void
    {
        $qb = $this->registry->repository($class)->createQueryBuilder('entity');

        foreach ($fields as $field) {
            $qb->orWhere('entity.'.$field.'.uuid IS NOT NULL');
        }

        $io->note($class);

        $progress = $io->createProgressBar();
        foreach (SimpleBatchIteratorAggregate::fromQuery($qb->getQuery(), 300) as $item) {
            /* @noinspection DisconnectedForeachInstructionInspection */
            $progress->advance();
        }
        $progress->finish();
    }
}
