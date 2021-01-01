<?php

declare(strict_types=1);

namespace App\Google\Messages;

use App\Google\Entity\Review;
use App\Google\Enum\StarRating;
use App\MessageBus\MessageHandler;
use App\Shared\Doctrine\Registry;
use App\Tenant\Tenant;
use function array_key_exists;
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
        $author = Markdown::code($payload['reviewer']['displayName']);

        if (array_key_exists('comment', $payload)) {
            $comment = $payload['comment'];

            $transPos = strpos($comment, '(Translated by Google)');
            if (is_int($transPos)) {
                $comment = substr($comment, 0, $transPos);
            }

            $text = Markdown::builder()
                ->p('Новый отзыв в Google от '.$author)
                ->p(trim($comment))
                ->p(sprintf('Оценка: %s', StarRating::fromGoogleValue($payload['starRating'])->toId()))
                ->getMarkdown();
        } else {
            $text = Markdown::builder()
                ->p('Новая оценка в Google от '.$author)
                ->p(sprintf('Оценка: %s', StarRating::fromGoogleValue($payload['starRating'])->toId()))
                ->getMarkdown();
        }

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
