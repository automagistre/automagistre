<?php

declare(strict_types=1);

namespace App\Sms\Messages;

use App\MessageBus\MessageHandler;
use App\Shared\Doctrine\Registry;
use App\Sms\Entity\Sms;
use App\Sms\Entity\SmsSend;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use function Sentry\captureException;

final class SendRequestedHandler implements MessageHandler
{
    private Registry $registry;

    private PhoneNumberUtil $phoneNumberUtil;

    private HttpClientInterface $httpClient;

    private RouterInterface $router;

    public function __construct(
        Registry $registry,
        PhoneNumberUtil $phoneNumberUtil,
        HttpClientInterface $httpClient,
        RouterInterface $router
    ) {
        $this->registry = $registry;
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->httpClient = $httpClient;
        $this->router = $router;
    }

    public function __invoke(SendRequested $event): void
    {
        /** @var Sms $sms */
        $sms = $this->registry->get(Sms::class, $event->smsId);

        $response = $this->httpClient->request('POST', 'sms/send', [
            'json' => [
                'number' => $this->phoneNumberUtil->format($sms->phoneNumber, PhoneNumberFormat::E164),
                'sign' => 'AVTOMAGISTR',
                'text' => $sms->message,
                'channel' => 'DIRECT',
                'callbackUrl' => $this->router->generate('sms_callback', [
                    'provider' => 'smsaero',
                    'id' => $event->smsId->toString(),
                ], RouterInterface::ABSOLUTE_URL),
                'dateSend' => null === $sms->dateSend ? '' : $sms->dateSend->getTimestamp(),
            ],
        ]);

        try {
            $response->getContent();
        } catch (Throwable $e) {
            captureException($e);
        }

        try {
            $content = $response->toArray(false);
        } catch (DecodingExceptionInterface $e) {
            $content = $response->getContent(false);
        }

        $payload = [
            'status_code' => $response->getStatusCode(),
            'content' => $content,
        ];

        $em = $this->registry->manager(SmsSend::class);
        $em->persist(new SmsSend($event->smsId, 200 === $response->getStatusCode(), $payload));
        $em->flush();
    }
}
