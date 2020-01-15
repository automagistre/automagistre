<?php

namespace App\Order\Twig;

use App\Car\Entity\Recommendation;
use App\Doctrine\Registry;
use App\Entity\Tenant\OrderItemService;
use App\State;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderExtension extends AbstractExtension
{
    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @var State
     */
    private State $state;

    public function __construct(Registry $registry, State $state)
    {
        $this->registry = $registry;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'recommendation_by_service',
                fn (OrderItemService $service) => $this->registry
                    ->repository(Recommendation::class)
                    ->findOneBy([
                        'realization.id' => $service->getId(),
                        'realization.tenant' => $this->state->tenant(),
                    ], ['id' => 'DESC'])
            ),
        ];
    }
}
