<?php

declare(strict_types=1);

namespace App\JSONRPC;

use App\ArgumentResolver\ArgumentResolverInterface;
use function assert;
use Datto\JsonRpc\Evaluator;
use Datto\JsonRpc\Exceptions\ApplicationException;
use Datto\JsonRpc\Exceptions\MethodException;
use function get_class;
use function is_array;
use function is_callable;
use function is_object;
use LogicException;
use function method_exists;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Psr\Container\ContainerInterface;
use Sentry\SentryBundle\SentryBundle;
use function sprintf;
use Throwable;

final class ApiEvaluator implements Evaluator
{
    private ContainerInterface $container;

    private ArgumentResolverInterface $argumentResolver;

    public function __construct(ContainerInterface $container, ArgumentResolverInterface $argumentResolver)
    {
        $this->container = $container;
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate($method, $arguments)
    {
        if (!$this->container->has($method)) {
            throw new MethodException();
        }

        $callable = $this->container->get($method);
        assert(is_callable($callable));

        $args = $this->argumentResolver->getArguments($arguments, $callable);

        try {
            $response = $callable(...$args);

            if (is_array($response)) {
                return $response;
            }

            if (is_object($response) && method_exists($response, 'toArray')) {
                return $response->toArray();
            }

            throw new LogicException(sprintf('Can\'t normalize response object %s', get_class($response)));
        } catch (OutOfRangeCurrentPageException $e) {
            throw new ArgumentException(['message' => $e->getMessage()]);
        } catch (Throwable $e) {
            SentryBundle::getCurrentHub()->captureException($e);

            throw new ApplicationException($e->getMessage(), (int) $e->getCode());
        }
    }
}
