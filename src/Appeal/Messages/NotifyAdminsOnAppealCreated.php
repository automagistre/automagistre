<?php

declare(strict_types=1);

namespace App\Appeal\Messages;

use App\Appeal\Entity\AppealView;
use App\Appeal\Event\AppealCreated;
use App\MessageBus\MessageHandler;
use App\Shared\Doctrine\Registry;
use App\Tenant\State;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Premier\MarkdownBuilder\Markdown;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function sprintf;

final class NotifyAdminsOnAppealCreated implements MessageHandler
{
    public function __construct(
        private Registry $registry,
        private RouterInterface $router,
        private State $state,
        private HttpClientInterface $httpClient,
        private string $telegramBotToken,
    ) {
    }

    public function __invoke(AppealCreated $event): void
    {
        $appealId = $event->appealId;
        /** @var AppealView $appealView */
        $appealView = $this->registry->get(AppealView::class, $appealId);

        $text = Markdown::builder()
            ->p('Поступила новая заявка')
            ->p(sprintf('%s от %s', Markdown::bold($appealView->type->toDisplayName()), $appealView->name))
            ->p(
                null !== $appealView->phone
                    ? PhoneNumberUtil::getInstance()->format($appealView->phone, PhoneNumberFormat::E164)
                    : $appealView->email ?? 'Контактная информация отсутствует',
            )
            ->getMarkdown()
        ;

        $this->httpClient->request(
            'POST',
            sprintf('https://api.telegram.org/bot%s/sendMessage', $this->telegramBotToken),
            [
                'json' => [
                    'chat_id' => $this->state->get()->toTelegramChannel(),
                    'disable_web_page_preview' => 1,
                    'parse_mode' => 'Markdown',
                    'text' => $text,
                    'reply_markup' => [
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => 'Открыть заявку',
                                    'url' => $this->router->generate('easyadmin', [
                                        'id' => $appealId->toString(),
                                        'entity' => 'Appeal',
                                        'action' => 'show',
                                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        );
    }
}
