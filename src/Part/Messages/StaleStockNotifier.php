<?php

declare(strict_types=1);

namespace App\Part\Messages;

use App\Doctrine\Registry;
use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderItemPart;
use App\Order\Event\OrderItemPartCreated;
use App\Part\Entity\PartView;
use App\Tenant\State;
use Premier\MarkdownBuilder\Block\NumberedListBuilder;
use Premier\MarkdownBuilder\Markdown;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function sprintf;

final class StaleStockNotifier implements MessageHandler
{
    public function __construct(
        private Registry $registry,
        private RouterInterface $router,
        private State $state,
        private HttpClientInterface $httpClient,
        private string $telegramBotToken,
    ) {
    }

    public function __invoke(OrderItemPartCreated $event): void
    {
        return;
        $telegramChannel = $this->state->get()->toTelegramChannel();

        if ('' === $telegramChannel) {
            return;
        }

        $orderItemPart = $this->registry->find(OrderItemPart::class, $event->itemId);

        if (null === $orderItemPart) {
            return;
        }

        $part = $this->registry->get(PartView::class, $orderItemPart->getPartId());

        if (!$part->hasKeepingStock()) {
            return;
        }

        if ($part->analogs->isEmpty()) {
            return;
        }

        /** @var PartView[] $canReplacedBy */
        $canReplacedBy = [];
        foreach ($part->analogs as $analog) {
            if ($analog->hasKeepingStock()) {
                continue;
            }

            if (($analog->quantity - $analog->ordered) < $orderItemPart->getQuantity()) {
                continue;
            }

            $canReplacedBy[] = $analog;
        }

        if ([] === $canReplacedBy) {
            return;
        }

        $text = Markdown::builder()
            ->p(
                Markdown::link(
                    $this->router->generate(
                        'easyadmin',
                        [
                            'id' => $orderItemPart->getOrder()->toId()->toString(),
                            'entity' => 'Order',
                            'action' => 'show',
                        ],
                        RouterInterface::ABSOLUTE_URL,
                    ),
                    sprintf('Заказ №%s', $orderItemPart->getOrder()->getNumber()),
                ),
            )
            ->p(sprintf(
                'Добавлена запчасть %s, однако на складе имеются залежавшиеся аналоги, которые нужно продать в первую очередь:',
                Markdown::link(
                    $this->router->generate(
                        'easyadmin',
                        [
                            'id' => $orderItemPart->toId()->toString(),
                            'entity' => 'OrderItemPart',
                            'action' => 'edit',
                        ],
                        RouterInterface::ABSOLUTE_URL,
                    ),
                    $part->display(),
                ),
            ))
            ->numberedList(function (NumberedListBuilder $builder) use ($canReplacedBy): void {
                foreach ($canReplacedBy as $analog) {
                    $builder->addLine(sprintf(
                        '%s - %s',
                        $analog->displayWithStock(),
                        Markdown::link(
                            $this->router->generate(
                                'easyadmin',
                                [
                                    'id' => $analog->toId()->toString(),
                                    'entity' => 'Part',
                                    'action' => 'show',
                                ],
                                RouterInterface::ABSOLUTE_URL,
                            ),
                            'ссылка',
                        ),
                    ));
                }
            })
            ->getMarkdown()
        ;

        $this->httpClient->request(
            'POST',
            sprintf('https://api.telegram.org/bot%s/sendMessage', $this->telegramBotToken),
            [
                'json' => [
                    'chat_id' => $telegramChannel,
                    'disable_web_page_preview' => 1,
                    'parse_mode' => 'Markdown',
                    'text' => $text,
                ],
            ],
        );
    }
}
