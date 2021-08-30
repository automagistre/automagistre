<?php

declare(strict_types=1);

namespace App\Customer\View;

use App\Customer\Entity\CustomerView;
use App\Customer\Entity\OperandId;
use App\Doctrine\Registry;
use App\Identifier\IdentifierFormatter;
use App\Identifier\IdentifierFormatterInterface;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Premier\Identifier\Identifier;
use function strtr;

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
        $operand = $this->registry->get(CustomerView::class, $identifier);

        $telephone = $operand->telephone;

        $values = [
            ':name:' => $operand->fullName,
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
