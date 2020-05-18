<?php

declare(strict_types=1);

namespace App\Shared\Mercure\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MercureExtension extends AbstractExtension
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('mercure_hub_url', [$this, 'mercureHubUrl']),
        ];
    }

    public function mercureHubUrl(): string
    {
        return $this->parameterBag->get('mercure.hub.url');
    }
}
