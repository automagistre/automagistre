<?php

declare(strict_types=1);

namespace App\Router;

use BadMethodCallException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class BrandRouter implements RouterInterface, RequestMatcherInterface, WarmableInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        $router = $this->router;
        $route = $router->getRouteCollection()->get($name);

        if ($route instanceof Route && !array_key_exists('brand', $parameters) && $route->hasRequirement('brand')) {
            $request = $this->requestStack->getCurrentRequest();

            $brand = $request->attributes->get('brand');
            if (!$brand) {
                return $this->generate('www_switch');
            }

            $parameters['brand'] = $brand;
        }

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
     * {@inheritdoc}
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
