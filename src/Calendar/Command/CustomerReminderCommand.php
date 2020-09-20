<?php

declare(strict_types=1);

namespace App\Calendar\Command;

use App\Calendar\Entity\EntryView;
use App\Costil;
use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\Registry;
use App\Sms\Action\SendSmsCommand;
use App\Sms\Enum\Feature;
use App\Tenant\Tenant;
use App\User\Entity\UserId;
use App\User\Security\ConsoleAuthenticator;
use DateTimeImmutable;
use function str_replace;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CustomerReminderCommand extends Command
{
    protected static $defaultName = 'calendar:customer:reminder';

    private Registry $registry;

    private MessageBusInterface $messageBus;

    private Tenant $tenant;

    private ConsoleAuthenticator $authenticator;

    public function __construct(
        Registry $registry,
        MessageBusInterface $commandBus,
        Tenant $tenant,
        ConsoleAuthenticator $authenticator
    ) {
        $this->registry = $registry;

        parent::__construct();
        $this->messageBus = $commandBus;
        $this->tenant = $tenant;
        $this->authenticator = $authenticator;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tomorrow = new DateTimeImmutable('+1 day');
        $rows = $this->registry->connection(EntryView::class)
            ->fetchAll('
                SELECT schedule_date AS date, order_info_customer_id AS "customerId"
                FROM calendar_entry_view
                WHERE order_info_customer_id IS NOT NULL
                      AND schedule_date BETWEEN :start AND :end',
                [
                    'start' => $tomorrow->setTime(0, 0, 0),
                    'end' => $tomorrow->setTime(23, 59, 59),
                ],
                [
                    'start' => 'datetime',
                    'end' => 'datetime',
                ]
            );

        $this->authenticator->authenticate(UserId::fromString(Costil::SERVICE_USER));

        foreach ($rows as $row) {
            /** @var DateTimeImmutable $date */
            $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['date']);
            $customerId = OperandId::fromString($row['customerId']);

            $message = str_replace(
                [
                    '{time}',
                ],
                [
                    $date->format('H:i'),
                ],
                $this->tenant->toSmsOnReminderEntry(),
            );

            $this->messageBus->dispatch(
                new SendSmsCommand(
                    $customerId,
                    $message,
                    [
                        Feature::onceADay(),
                    ]
                )
            );
        }

        return 0;
    }
}
