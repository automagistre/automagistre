<?php

declare(strict_types=1);

namespace App\Customer\View;

use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use function array_keys;
use function array_values;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use function sprintf;
use function str_replace;
use function trim;

final class OperandFormatter implements IdentifierFormatterInterface
{
    private const DEFAULT = ':name:';
    private const FORMATS = [
        'tel' => ':tel:',
        'autocomplete' => ':name: | :tel:',
    ];

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
        $isPerson = '1' === $view['type'];

        $values = [
            ':name:' => $isPerson
                ? trim(sprintf('%s %s', $view['lastname'], $view['firstname']))
                : $view['name'],
            ':tel:' => null !== $view['telephone']
                ? $this->phoneNumberUtil->format($view['telephone'], PhoneNumberFormat::NATIONAL)
                : '-',
        ];

        return str_replace(array_keys($values), array_values($values), self::FORMATS[$format] ?? self::DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return OperandId::class;
    }
}
