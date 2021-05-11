<?php

declare(strict_types=1);

namespace App\Employee\Command;

use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Enum\CustomerTransactionSource;
use App\Employee\Entity\Employee;
use App\Employee\Entity\SalaryView;
use App\Shared\Doctrine\Registry;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;
use function date;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SalaryChargeCommand extends Command
{
    protected static $defaultName = 'employee:salary:charge';

    public function __construct(private Registry $registry, private EventDispatcherInterface $dispatcher)
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->addArgument('payday', InputArgument::OPTIONAL)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $payday */
        $payday = $input->getArgument('payday') ?? date('j');

        try {
            $this->paySalary($payday);
        } catch (Throwable $e) {
            $event = new ConsoleErrorEvent($input, $output, $e, $this);

            $this->dispatcher->dispatch($event);
        }

        return 0;
    }

    private function paySalary(string $payday): void
    {
        /** @var SalaryView[] $salaries */
        $salaries = $this->registry->repository(SalaryView::class)
            ->createQueryBuilder('entity')
            ->join(Employee::class, 'employee', Join::WITH, 'employee.id = entity.employeeId')
            ->where('employee.firedAt IS NULL')
            ->andWhere('entity.payday = :payday')
            ->andWhere('entity.ended IS NULL')
            ->getQuery()
            ->setParameter('payday', $payday)
            ->getResult()
        ;

        $em = $this->registry->manager(CustomerTransaction::class);

        foreach ($salaries as $salary) {
            $em->persist(
                new CustomerTransaction(
                    CustomerTransactionId::generate(),
                    $salary->personId,
                    $salary->amount,
                    CustomerTransactionSource::salary(),
                    $salary->id->toUuid(),
                    null,
                ),
            );
        }

        $em->flush();
    }
}
