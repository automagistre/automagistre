<?php

declare(strict_types=1);

namespace App\Shared\Router;

use function array_key_exists;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use function urlencode;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartExplorerRouteRefererListener implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RoutePreGenerate::class => 'onRouterPreGenerate',
        ];
    }

    public function onRouterPreGenerate(GenericEvent $event): void
    {
        ['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType] = $event->getArguments();

        if ('part_explorer' !== $name) {
            return;
        }

        if (array_key_exists('referer', $parameters)) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        if ($request->query->has('referer')) {
            $parameters['referer'] = $request->query->get('referer');
        } else {
            $parameters['referer'] = urlencode($request->getUri());
        }

        $event->setArguments(['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType]);
    }
}
