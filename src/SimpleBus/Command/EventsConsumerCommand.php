<?php

declare(strict_types=1);

namespace App\SimpleBus\Command;

use Amp\Loop;
use App\Nsq\Envelop;
use App\Nsq\Nsq;
use App\Tenant\Tenant;
use function class_exists;
use Generator;
use function is_object;
use LogicException;
use LongRunning\Core\Cleaner;
use Sentry\Util\JSON;
use const SIGINT;
use const SIGTERM;
use SimpleBus\SymfonyBridge\Bus\EventBus;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class EventsConsumerCommand extends Command
{
    protected static $defaultName = 'events:consume';

    private Nsq $nsq;

    private Tenant $tenant;

    private DenormalizerInterface $denormalizer;

    private EventBus $eventBus;

    private Cleaner $cleaner;

    public function __construct(
        Nsq $nsq,
        Tenant $tenant,
        DenormalizerInterface $denormalizer,
        EventBus $eventBus,
        Cleaner $cleaner
    ) {
        parent::__construct();

        $this->nsq = $nsq;
        $this->tenant = $tenant;
        $this->denormalizer = $denormalizer;
        $this->eventBus = $eventBus;
        $this->cleaner = $cleaner;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $topic = sprintf('%s_events', $this->tenant->toIdentifier());

        Loop::run(function () use ($topic, $io): void {
            $stopper = $this->nsq->subscribe($topic, 'tenant', function (Envelop $envelop) use ($io): Generator {
                $data = JSON::decode($envelop->body);

                $class = $data['class'] ?? '';
                if (!class_exists($class)) {
                    throw new LogicException(sprintf('Event class "%s" not exists. Body: "%s"', $class, $envelop->body));
                }

                $event = $this->denormalizer->denormalize($data['body'], $class);
                if (!is_object($event)) {
                    throw new LogicException(sprintf('Event class "%s" not exists. Body: "%s"', $class, $envelop->body));
                }

                $this->eventBus->handle($event);

                $io->success(sprintf('Event: %s handled.', $class));

                $this->cleaner->cleanUp();

                yield $envelop->ack();
            });

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
