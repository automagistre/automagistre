<?php

declare(strict_types=1);

namespace App\SimpleBus\Command;

use function Amp\ByteStream\getStdout;
use Amp\Loop;
use App\Nsq\Envelop;
use App\Nsq\Nsq;
use App\SimpleBus\Serializer\MessageSerializer;
use App\Tenant\Tenant;
use App\User\Security\ConsoleAuthenticator;
use function date;
use const DATE_RFC3339;
use Generator;
use function get_class;
use function implode;
use LongRunning\Core\Cleaner;
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

        Loop::run(function () use ($io): void {
            $stopwatch = new Stopwatch();
            $stdout = getStdout();

            $stopper = $this->nsq->subscribe(
                $this->tenant->toBusTopic(),
                'tenant',
                function (Envelop $envelop) use ($stopwatch, $stdout): Generator {
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

                        yield $envelop->ack();

                        $isSuccess = true;
                    } catch (Throwable $e) {
                        captureException($e);

                        yield $envelop->retry(
                            ($envelop->attempts <= 60 ? $envelop->attempts : 60) * 1000
                        );
                    } finally {
                        $this->cleaner->cleanUp();
                        $event->stop();
                        $this->authenticator->invalidate();
                    }

                    yield $stdout->write(implode(' ',
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
            );

            $onSignal = static function () use ($stopper, $io): void {
                $io->note('Stop signal received');

                $stopper->stop();

                Loop::delay(1000, static function (): void {
                    Loop::stop();
                });
            };

            Loop::onSignal(SIGINT, $onSignal);
            Loop::onSignal(SIGTERM, $onSignal);
        });

        return 0;
    }
}
