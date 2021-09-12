<?php

declare(strict_types=1);

namespace App\Calendar\Command;

use App\Calendar\Entity\EntryView;
use App\Doctrine\Registry;
use App\Sms\Enum\Feature;
use App\Sms\Messages\SendSms;
use App\Tenant\State;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use LogicException;
use function str_replace;

final class CustomerReminderCommand extends Command
{
    protected static $defaultName = 'calendar:customer:reminder';

    public function __construct(
        private Registry $registry,
        private MessageBusInterface $messageBus,
        private State $state,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tomorrow = new DateTimeImmutable('+1 day');

        /** @var EntryView[] $entries */
        $entries = $this->registry->manager()
            ->createQueryBuilder()
            ->select('t')
            ->from(EntryView::class, 't')
            ->where('t.orderInfo.customerId IS NOT NULL')
            ->andWhere('t.schedule.date BETWEEN :start AND :end')
            ->getQuery()
            ->setParameter('start', $tomorrow->setTime(0, 0, 0), 'datetime')
            ->setParameter('end', $tomorrow->setTime(23, 59, 59), 'datetime')
            ->getResult()
        ;

        foreach ($entries as $entry) {
            $message = str_replace(
                [
                    '{time}',
                ],
                [
                    $entry->schedule->date->format('H:i'),
                ],
                $this->state->get()->toSmsOnReminderEntry(),
            );

            $this->messageBus->dispatch(
                new SendSms(
                    $entry->orderInfo->customerId ?? throw new LogicException(),
                    $message,
                    [
                        Feature::onceADay(),
                    ],
                ),
            );
        }

        return 0;
    }
}
