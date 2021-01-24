<?php

declare(strict_types=1);

namespace App;

use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use LogicException;
use Money\Currency;
use Money\Money;
use function array_key_exists;
use function str_contains;
use function str_replace;

/**
 * Сборник костылей.
 */
final class Costil
{
    public const OLD_USER = '4ffc24e2-8e60-42e0-9c8f-7a73888b2da6';
    public const SERVICE_USER = '59861141-83b2-416c-b672-8ba8a1cb76b2';

    public static IdentifierFormatter $formatter;

    private function __construct()
    {
        throw new LogicException('SonarQube сказал надо исключение кидать.');
    }

    /**
     * Monkey migration. EasyAdminAutocompleteType require entity with __toString.
     */
    public static function display(Identifier $identifier, string $format = null): string
    {
        return self::$formatter->format($identifier, $format);
    }

    public static function convertToMoney(array $array): array
    {
        foreach ($array as $key => $value) {
            if (!str_contains($key, 'currency.code')) {
                continue;
            }

            $moneyKey = str_replace('.currency.code', '', $key);

            if (!array_key_exists($moneyKey.'.amount', $array)) {
                continue;
            }

            $array[$moneyKey] = null !== $array[$moneyKey.'.amount']
                ? new Money(
                    $array[$moneyKey.'.amount'],
                    new Currency($array[$moneyKey.'.currency.code']),
                )
                : null;

            unset($array[$moneyKey.'.amount'], $array[$moneyKey.'.currency.code']);
        }

        return $array;
    }
}
