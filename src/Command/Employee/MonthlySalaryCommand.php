<?php

declare(strict_types=1);

namespace App\Command\Employee;

use App\Doctrine\Registry;
use App\Entity\Landlord\User;
use App\Entity\Tenant\MonthlySalary;
use App\Manager\PaymentManager;
use App\State;
use App\Tenant\Tenant;
use function date;
use InvalidArgumentException;
use function is_string;
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

    private State $state;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        State $state,
        Registry $registry,
        PaymentManager $paymentManager,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct();

        $this->registry = $registry;
        $this->paymentManager = $paymentManager;
        $this->state = $state;
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
        $payday = $input->getArgument('payday') ?? date('j');
        $description = $input->getOption('description') ?? '# Начисление ежемесячного оклада';

        if (!is_string($payday) || !is_string($description)) {
            throw new InvalidArgumentException('Payday and Description required.');
        }

        $user = $this->registry->repository(User::class)->findOneBy(['username' => 'service@automagistre.ru']);
        if (!$user instanceof User) {
            throw new RuntimeException('Service user not found.');
        }
        $this->state->user($user);

        /** @var Tenant $tenant */
        foreach (Tenant::all() as $tenant) {
            try {
                $this->state->tenant($tenant);

                $this->paySalary($payday, $description);
            } catch (Throwable $e) {
                $event = new ConsoleErrorEvent($input, $output, $e, $this);

                $this->dispatcher->dispatch($event);
            }
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

        foreach ($salaries as $salary) {
            $person = $salary->getEmployee()->getPerson();

            $desc = $description.' #'.$salary->getId();
            $this->paymentManager->createPayment($person, $desc, $salary->getAmount());
        }
    }
}
