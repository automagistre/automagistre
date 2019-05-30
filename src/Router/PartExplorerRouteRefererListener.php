<?php

declare(strict_types=1);

namespace App\Router;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartExplorerRouteRefererListener implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

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

        if ('admin_part_explorer' !== $name) {
            return;
        }

        if (\array_key_exists('referer', $parameters)) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        if ($request->query->has('referer')) {
            $parameters['referer'] = $request->query->get('referer');
        } else {
            $parameters['referer'] = \urlencode($request->getUri());
        }

        $event->setArguments(\compact('name', 'parameters', 'referenceType'));
    }
}
