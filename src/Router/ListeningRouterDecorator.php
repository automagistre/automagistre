<?php

declare(strict_types=1);

namespace App\Router;

use BadMethodCallException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class ListeningRouterDecorator implements RouterInterface, RequestMatcherInterface, WarmableInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(RouterInterface $router, EventDispatcherInterface $dispatcher)
    {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        $router = $this->router;
        $dispatcher = $this->dispatcher;

        $event = new GenericEvent($router, compact('name', 'parameters', 'referenceType'));
        $dispatcher->dispatch(ListeningRouterEvents::PRE_GENERATE, $event);
        ['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType] = $event->getArguments();

        return $router->generate($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context): void
    {
        $this->router->setContext($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext(): RequestContext
    {
        return $this->router->getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(): RouteCollection
    {
        return $this->router->getRouteCollection();
    }

    /**
     * @param string $pathinfo
     *
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException
     * @throws \Symfony\Component\Routing\Exception\NoConfigurationException
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function match($pathinfo): array
    {
        return $this->router->match($pathinfo);
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request): array
    {
        if (!$this->router instanceof RequestMatcherInterface) {
            throw new BadMethodCallException('Router has to implement the '.RequestMatcherInterface::class);
        }

        return $this->router->matchRequest($request);
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir): void
    {
        if ($this->router instanceof WarmableInterface) {
            $this->router->warmUp($cacheDir);
        }
    }
}
