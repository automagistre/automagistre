<?php

declare(strict_types=1);

namespace App\Employee\Command;

use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Enum\CustomerTransactionSource;
use App\Employee\Entity\MonthlySalary;
use App\Payment\Manager\PaymentManager;
use App\Shared\Doctrine\Registry;
use App\User\Entity\User;
use function assert;
use function date;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MonthlySalaryCommand extends Command
{
    protected static $defaultName = 'employee:monthly:salary';

    private Registry $registry;

    private PaymentManager $paymentManager;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        Registry $registry,
        PaymentManager $paymentManager,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct();

        $this->registry = $registry;
        $this->paymentManager = $paymentManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->addArgument('payday', InputArgument::OPTIONAL)
            ->addOption('description', 'd', InputOption::VALUE_OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $payday */
        $payday = $input->getArgument('payday') ?? date('j');
        /** @var string $description */
        $description = $input->getOption('description') ?? '# Начисление ежемесячного оклада';

        $user = $this->registry->repository(User::class)->findOneBy(['username' => 'service@automagistre.ru']);
        if (!$user instanceof User) {
            throw new RuntimeException('Service user not found.');
        }

        try {
            $this->paySalary($payday, $description);
        } catch (Throwable $e) {
            $event = new ConsoleErrorEvent($input, $output, $e, $this);

            $this->dispatcher->dispatch($event);
        }

        return 0;
    }

    private function paySalary(string $payday, string $description): void
    {
        /** @var MonthlySalary[] $salaries */
        $salaries = $this->registry->repository(MonthlySalary::class)
            ->createQueryBuilder('entity')
            ->join('entity.employee', 'employee')
            ->where('employee.firedAt IS NULL')
            ->andWhere('entity.payday = :payday')
            ->andWhere('entity.endedAt IS NULL')
            ->getQuery()
            ->setParameter('payday', $payday)
            ->getResult();

        $em = $this->registry->manager(CustomerTransaction::class);

        foreach ($salaries as $salary) {
            $person = $salary->getEmployee()->getPerson();
            assert(null !== $person);

            $em->persist(
                new CustomerTransaction(
                    CustomerTransactionId::generate(),
                    $person->toId(),
                    $salary->getAmount(),
                    CustomerTransactionSource::salary(),
                    $salary->toId(),
                    null
                )
            );
        }

        $em->flush();
    }
}
