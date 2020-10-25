<?php

declare(strict_types=1);

namespace App\Sms\Messages;

use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandId;
use App\MessageBus\MessageHandler;
use App\Shared\Doctrine\Registry;
use App\Sms\Entity\Sms;
use App\Sms\Enum\Feature;
use App\Tenant\Tenant;
use DateTimeImmutable;

final class SendSmsHandler implements MessageHandler
{
    private Tenant $tenant;

    private Registry $registry;

    public function __construct(Tenant $tenant, Registry $registry)
    {
        $this->tenant = $tenant;
        $this->registry = $registry;
    }

    public function __invoke(SendSms $command): void
    {
        if (!$this->tenant->isSmsEnabled()) {
            return;
        }

        if ($command->recipient instanceof OperandId) {
            $phoneNumber = $this->registry->getBy(Operand::class, $command->recipient)->getTelephone();
        } else {
            $phoneNumber = $command->recipient;
        }

        if (null === $phoneNumber) {
            // TODO Info log number not exists

            return;
        }

        if ($command->hasFeature(Feature::onceADay())) {
            $result = $this->registry->connection(Sms::class)
                ->fetchOne(
                    'SELECT 1
                                FROM sms_send ss 
                                    JOIN created_by cb ON cb.id = ss.sms_id
                                    JOIN sms s ON s.id = ss.sms_id 
                                                      AND success IS TRUE 
                                                      AND s.phone_number = :phone
                                WHERE cb.created_at BETWEEN :start AND :end',
                    [
                        'phone' => $phoneNumber,
                        'start' => (new DateTimeImmutable())->setTime(0, 0, 0),
                        'end' => (new DateTimeImmutable())->setTime(23, 59, 59),
                    ],
                    [
                        'phone' => 'phone_number',
                        'start' => 'datetime',
                        'end' => 'datetime',
                    ]
                );

            if (1 === $result) {
                // TODO Info log

                return;
            }
        }

        $this->registry->manager(Sms::class)->persist(
            new Sms(
                $phoneNumber,
                $command->message,
                $command->dateSend,
            )
        );
    }
}
