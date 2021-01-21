<?php

declare(strict_types=1);

namespace App\Review\Messages;

use App\MessageBus\MessageHandler;
use App\Review\Entity\Review;
use App\Review\Event\ReviewReceived;
use App\Shared\Doctrine\Registry;
use App\Tenant\Tenant;
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

        $author = Markdown::code($review->author);
        $sourceName = Markdown::bold($review->source->toDisplayName());

        $markdown = Markdown::builder();
        if ('' !== $review->text) {
            $markdown
                ->p(sprintf('Новый отзыв в %s от %s', $sourceName, $author))
                ->p($review->text);
        } else {
            $markdown
                ->p(sprintf('Новая оценка в %s от %s', $sourceName, $author));
        }

        $markdown->p(sprintf('Оценка: %s', $review->rating->toId()));

        $this->httpClient->request(
            'POST',
            sprintf('https://api.telegram.org/bot%s/sendMessage', $this->telegramBotToken),
            [
                'json' => [
                    'chat_id' => $this->tenant->toTelegramChannel(),
                    'disable_web_page_preview' => 1,
                    'parse_mode' => 'Markdown',
                    'text' => $markdown->getMarkdown(),
                ],
            ]
        );
    }
}
