<?php

declare(strict_types=1);

namespace App\Review\Messages;

use App\Doctrine\Registry;
use App\MessageBus\MessageHandler;
use App\Review\Entity\Review;
use App\Review\Event\ReviewReceived;
use App\Tenant\State;
use Premier\MarkdownBuilder\Markdown;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function sprintf;

final class TelegramNotifyOfNewReview implements MessageHandler
{
    public function __construct(
        private State $state,
        private Registry $registry,
        private HttpClientInterface $httpClient,
        private string $telegramBotToken,
    ) {
    }

    public function __invoke(ReviewReceived $event): void
    {
        $telegramChannel = $this->state->get()->toTelegramChannel();

        if ('' === $telegramChannel) {
            return;
        }

        /** @var Review $review */
        $review = $this->registry->find(Review::class, $event->reviewId);

        if (null === $review) {
            return;
        }

        $author = Markdown::code($review->author);
        $sourceName = Markdown::bold($review->source->toDisplayName());

        $markdown = Markdown::builder();

        if ('' !== $review->text) {
            $markdown
                ->p(sprintf('Новый отзыв в %s от %s', $sourceName, $author))
                ->p($review->text)
            ;
        } else {
            $markdown
                ->p(sprintf('Новая оценка в %s от %s', $sourceName, $author))
            ;
        }

        $markdown->p(sprintf('Оценка: %s', $review->rating->toId()));

        try {
            $this->httpClient->request(
                'POST',
                sprintf('https://api.telegram.org/bot%s/sendMessage', $this->telegramBotToken),
                [
                    'json' => [
                        'chat_id' => $telegramChannel,
                        'disable_web_page_preview' => 1,
                        'parse_mode' => 'Markdown',
                        'text' => $markdown->getMarkdown(),
                    ],
                ],
            );
        } catch (Throwable $e) {
            // skip
        }
    }
}
