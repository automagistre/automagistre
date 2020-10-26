<?php

declare(strict_types=1);

namespace App\Yandex\Map\Messages;

use App\MessageBus\MessageHandler;
use App\Shared\Doctrine\Registry;
use App\Tenant\Tenant;
use App\Yandex\Map\Entity\Review;
use Premier\MarkdownBuilder\Markdown;
use function sprintf;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TelegramNotifyOfNewReview implements MessageHandler
{
    private Tenant $tenant;

    private Registry $registry;

    private HttpClientInterface $httpClient;

    private string $telegramBotToken;

    public function __construct(
        Tenant $tenant,
        Registry $registry,
        HttpClientInterface $httpClient,
        string $telegramBotToken
    ) {
        $this->tenant = $tenant;
        $this->registry = $registry;
        $this->httpClient = $httpClient;
        $this->telegramBotToken = $telegramBotToken;
    }

    public function __invoke(ReviewReceived $event): void
    {
        if ('' === $this->tenant->toTelegramChannel()) {
            return;
        }

        /** @var Review $review */
        $review = $this->registry->get(Review::class, $event->reviewId);

        $payload = $review->payload;
        $author = $payload['author'];

        $text = Markdown::builder()
            ->p('Новый отзыв от '.Markdown::link($author['profileUrl'], $author['name']))
            ->p($payload['text'])
            ->p(sprintf('Оценка: %s', $payload['rating']))
            ->getMarkdown();

        $this->httpClient->request(
            'POST',
            sprintf('https://api.telegram.org/bot%s/sendMessage', $this->telegramBotToken),
            [
                'json' => [
                    'chat_id' => $this->tenant->toTelegramChannel(),
                    'disable_web_page_preview' => 1,
                    'parse_mode' => 'Markdown',
                    'text' => $text,
                ],
            ]
        );
    }
}
