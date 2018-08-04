<?php

declare(strict_types=1);

namespace App\Router;

use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class BrandRoutesListener implements EventSubscriberInterface
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
            ListeningRouterEvents::PRE_GENERATE => 'onRouterPreGenerate',
        ];
    }

    public function onRouterPreGenerate(GenericEvent $event): void
    {
        $router = $event->getSubject();
        if (!$router instanceof RouterInterface) {
            throw new LogicException(sprintf('"%s" required.', RouterInterface::class));
        }

        ['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType] = $event->getArguments();

        $route = $router->getRouteCollection()->get($name);

        if (!$route instanceof Route) {
            return;
        }

        if (array_key_exists('brand', $parameters)) {
            return;
        }

        if (!$route->hasRequirement('brand')) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        $brand = $request->attributes->get('brand');
        if (null === $brand) {
            $name = 'www_switch';
            $parameters = [];
            $referenceType = RouterInterface::ABSOLUTE_PATH;
        } else {
            $parameters['brand'] = $brand;
        }

        $event->setArguments(compact('name', 'parameters', 'referenceType'));
    }
}
