<?php

declare(strict_types=1);

namespace App\Customer\Infrastructure;

use App\Customer\Domain\OperandId;
use App\Doctrine\ORM\Type\Identifier;
use App\Doctrine\Registry;
use App\Infrastructure\Identifier\IdentifierFormatter;
use App\Infrastructure\Identifier\IdentifierFormatterInterface;
use LogicException;
use function sprintf;
use function trim;

final class OperandFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $view = $this->registry->view($identifier);

        if ('1' === $view['type']) {
            $string = sprintf('%s %s', $view['lastname'], $view['firstname']);
            $email = $view['email'];

            if (null !== $email) {
                $string .= sprintf(' (%s)', $email);
            }

            return trim($string);
        }

        if ('2' === $view['type']) {
            return $view['name'];
        }

        throw new LogicException('Unreachable statement.');
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return OperandId::class;
    }
}
