<?php

declare(strict_types=1);

namespace App\Customer\View;

use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Premier\Identifier\Identifier;
use function strtr;
use function trim;

final class OperandFormatter implements IdentifierFormatterInterface
{
    private const DEFAULT = ':name:';
    private const FORMATS = [
        'tel' => ':tel:',
        'autocomplete' => ':name: | :tel:',
    ];

    public function __construct(private Registry $registry, private PhoneNumberUtil $phoneNumberUtil)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $operand = $this->registry->get(Operand::class, $identifier);

        $telephone = $operand->getTelephone();

        $values = [
            ':name:' => trim($operand->getFullName()),
            ':tel:' => null !== $telephone
                ? $this->phoneNumberUtil->format($telephone, PhoneNumberFormat::NATIONAL)
                : '-',
        ];

        return strtr(self::FORMATS[$format] ?? self::DEFAULT, $values);
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return OperandId::class;
    }
}
