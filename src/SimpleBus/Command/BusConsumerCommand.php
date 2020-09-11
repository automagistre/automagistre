<?php

declare(strict_types=1);

namespace App\SimpleBus\Command;

use App\Nsq\Envelop;
use App\Nsq\Nsq;
use App\SimpleBus\Serializer\MessageSerializer;
use App\Tenant\Tenant;
use App\User\Security\ConsoleAuthenticator;
use function date;
use const DATE_RFC3339;
use function get_class;
use function implode;
use LongRunning\Core\Cleaner;
use function pcntl_signal;
use function pcntl_signal_dispatch;
use const PHP_EOL;
use function Sentry\captureException;
use const SIGINT;
use const SIGTERM;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use SimpleBus\SymfonyBridge\Bus\EventBus;
use function sprintf;
use function substr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;
use Throwable;

final class BusConsumerCommand extends Command
{
    protected static $defaultName = 'bus:consume';

    private Nsq $nsq;

    private Tenant $tenant;

    private MessageSerializer $serializer;

    private CommandBus $commandBus;

    private EventBus $eventBus;

    private Cleaner $cleaner;

    private ConsoleAuthenticator $authenticator;

    public function __construct(
        Nsq $nsq,
        Tenant $tenant,
        MessageSerializer $serializer,
        CommandBus $commandBus,
        EventBus $eventBus,
        Cleaner $cleaner,
        ConsoleAuthenticator $authenticator
    ) {
        parent::__construct();

        $this->nsq = $nsq;
        $this->tenant = $tenant;
        $this->serializer = $serializer;
        $this->commandBus = $commandBus;
        $this->eventBus = $eventBus;
        $this->cleaner = $cleaner;
        $this->authenticator = $authenticator;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stopSignalReceived = false;
        $handler = static function () use ($io, &$stopSignalReceived): void {
            $io->writeln('Stop signal received.');

            $stopSignalReceived = true;
        };
        pcntl_signal(SIGTERM, $handler);
        pcntl_signal(SIGINT, $handler);

        $stopwatch = (new Stopwatch())->start('consumer');

        $generator = $this->nsq->subscribe($this->tenant->toBusTopic(), 'tenant');

        $io->note('Start listening...');

        foreach ($generator as $envelop) {
            if ($envelop instanceof Envelop) {
                $this->handleMessage($envelop, $io);
            }

            pcntl_signal_dispatch();
            if ($stopSignalReceived) {
                break;
            }
        }

        $io->success((string) $stopwatch);

        return 0;
    }

    private function handleMessage(Envelop $envelop, SymfonyStyle $io): void
    {
        $stopwatch = new Stopwatch();
        $event = $stopwatch->start($envelop->id);

        $decoded = $this->serializer->decode($envelop->body);
        $this->authenticator->authenticate($decoded->userId);

        /** @var string $messageClass */
        $messageClass = get_class($decoded->message);

        $isSuccess = false;
        try {
            if ('Command' === substr($messageClass, -7)) {
                $this->commandBus->handle($decoded);
            } else {
                $this->eventBus->handle($decoded);
            }

            $envelop->ack();

            $isSuccess = true;
        } catch (Throwable $e) {
            captureException($e);

            $envelop->retry(
                ($envelop->attempts <= 60 ? $envelop->attempts : 60) * 1000
            );
        } finally {
            $this->cleaner->cleanUp();
            $event->stop();
            $this->authenticator->invalidate();
        }

        $io->write(implode(' ',
            [
                date(sprintf('[%s]', DATE_RFC3339)),
                $decoded->trackingId,
                sprintf('[%s]', $isSuccess ? 'OK' : 'FAIL'),
                $messageClass,
                sprintf('%.2F MiB - %d ms', $event->getMemory() / 1024 / 1024, $event->getDuration()),
                PHP_EOL,
            ]
        ));
    }
}
