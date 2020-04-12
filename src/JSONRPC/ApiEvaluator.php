<?php

declare(strict_types=1);

namespace App\JSONRPC;

use App\ArgumentResolver\ArgumentResolverInterface;
use function array_map;
use function assert;
use Datto\JsonRpc\Evaluator;
use Datto\JsonRpc\Exceptions\ApplicationException;
use Datto\JsonRpc\Exceptions\Exception;
use Datto\JsonRpc\Exceptions\MethodException;
use function get_class;
use function is_array;
use function is_callable;
use function is_object;
use function iterator_to_array;
use LogicException;
use function method_exists;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Psr\Container\ContainerInterface;
use Sentry\SentryBundle\SentryBundle;
use function sprintf;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class ApiEvaluator implements Evaluator
{
    private ContainerInterface $container;

    private ArgumentResolverInterface $argumentResolver;

    private TranslatorInterface $translator;

    public function __construct(
        ContainerInterface $container,
        ArgumentResolverInterface $argumentResolver,
        TranslatorInterface $translator
    ) {
        $this->container = $container;
        $this->argumentResolver = $argumentResolver;
        $this->translator = $translator;
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

        try {
            $args = $this->argumentResolver->getArguments($arguments, $callable);
        } catch (ValidationException $e) {
            $errors = array_map(fn (ConstraintViolationInterface $violation) => [
                'field' => $violation->getPropertyPath(),
                'message' => $this->translator->trans(
                    $violation->getMessageTemplate(),
                    $violation->getParameters(),
                    'validators'
                ),
            ], iterator_to_array($e->violations));

            throw new ArgumentException($errors);
        }

        try {
            $response = $callable(...$args);

            if (is_array($response)) {
                return $response;
            }

            if (is_object($response) && method_exists($response, 'toArray')) {
                return $response->toArray();
            }

            throw new LogicException(sprintf('Can\'t normalize response object %s', get_class($response)));
        } catch (Exception $e) {
            throw $e;
        } catch (OutOfRangeCurrentPageException $e) {
            throw new ArgumentException(['message' => $e->getMessage()]);
        } catch (Throwable $e) {
            SentryBundle::getCurrentHub()->captureException($e);

            throw new ApplicationException($e->getMessage(), (int) $e->getCode());
        }
    }
}
