<?php

declare(strict_types=1);

namespace App\Part\Messages;

use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderItemPart;
use App\Order\Messages\OrderItemPartCreated;
use App\Part\Entity\PartView;
use App\Shared\Doctrine\Registry;
use App\Tenant\Tenant;
use Premier\MarkdownBuilder\Markdown;
use Premier\MarkdownBuilder\NumberedListBuilder;
use function sprintf;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class StaleStockNotifier implements MessageHandler
{
    private Registry $registry;

    private Tenant $tenant;

    private RouterInterface $router;

    private HttpClientInterface $httpClient;

    private string $telegramBotToken;

    public function __construct(
        Registry $registry,
        RouterInterface $router,
        Tenant $tenant,
        HttpClientInterface $httpClient,
        string $telegramBotToken
    ) {
        $this->registry = $registry;
        $this->tenant = $tenant;
        $this->router = $router;
        $this->httpClient = $httpClient;
        $this->telegramBotToken = $telegramBotToken;
    }

    public function __invoke(OrderItemPartCreated $event): void
    {
        if ('' === $this->tenant->toTelegramChannel()) {
            return;
        }

        /** @var OrderItemPart $orderItemPart */
        $orderItemPart = $this->registry->get(OrderItemPart::class, $event->id);

        /** @var PartView $part */
        $part = $this->registry->get(PartView::class, $orderItemPart->getPartId());

        if (!$part->hasKeepingStock()) {
            return;
        }

        if ([] === $part->analogs) {
            return;
        }

        /** @var PartView[] $analogs */
        $analogs = $this->registry->repository(PartView::class)->findBy(['id' => $part->analogs]);

        /** @var PartView[] $canReplacedBy */
        $canReplacedBy = [];
        foreach ($analogs as $analog) {
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

        $this->router->setContext(
            $this->router->getContext()
                ->setHost(sprintf('%s.automagistre.ru', $this->tenant->toIdentifier()))
                ->setScheme('https')
        );

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
                    sprintf('Заказ №%s', $orderItemPart->getOrder()->getNumber())
                )
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
                )
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
                            'ссылка'
                        ),
                    ));
                }
            })
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
