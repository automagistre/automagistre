<?php

declare(strict_types=1);

namespace EasyCorp\Bundle\EasyAdminBundle\Exception;

use Exception;
use RuntimeException;
use Symfony\Component\ErrorHandler\Exception\FlattenException as BaseFlattenException;
use function get_class;

/**
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class FlattenException extends BaseFlattenException
{
    /** @var ExceptionContext */
    private $context;

    /**
     * @throws RuntimeException
     */
    public static function create(Exception $exception, int $statusCode = null, array $headers = []): static
    {
        if (!$exception instanceof BaseException) {
            throw new RuntimeException(sprintf('You should only try to create an instance of "%s" with a "EasyCorp\Bundle\EasyAdminBundle\Exception\BaseException" instance, or subclass. "%s" given.', __CLASS__, get_class($exception)));
        }

        $e = parent::create($exception, $statusCode, $headers);
        $e->context = $exception->getContext();

        return $e;
    }

    public function getPublicMessage()
    {
        return $this->context->getPublicMessage();
    }

    public function getDebugMessage()
    {
        return $this->context->getDebugMessage();
    }

    public function getParameters()
    {
        return $this->context->getParameters();
    }

    public function getTranslationParameters()
    {
        return $this->context->getTranslationParameters();
    }

    public function getStatusCode(): int
    {
        return $this->context->getStatusCode();
    }
}
