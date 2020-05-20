<?php

declare(strict_types=1);

namespace App\Customer\Infrastructure;

use App\Customer\Domain\OperandId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use LogicException;
use function sprintf;
use function trim;

final class OperandFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    private PhoneNumberUtil $phoneNumberUtil;

    public function __construct(Registry $registry, PhoneNumberUtil $phoneNumberUtil)
    {
        $this->registry = $registry;
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $view = $this->registry->view($identifier);

        if ('tel' === $format && null !== $view['telephone']) {
            return $this->phoneNumberUtil->format($view['telephone'], PhoneNumberFormat::NATIONAL);
        }

        if ('1' === $view['type']) {
            return trim(sprintf('%s %s', $view['lastname'], $view['firstname']));
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
