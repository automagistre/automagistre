<?php

declare(strict_types=1);

namespace App\Tests\Form\Type;

use App\Form\Type\MoneyType;
use Generator;
use Money\Money;
use Symfony\Component\Form\Test\TypeTestCase;

final class MoneyTypeTest extends TypeTestCase
{
    /**
     * @dataProvider valid
     */
    public function testSubmitValidData(?Money $initial, ?string $viewed, string $submit, Money $expected): void
    {
        $form = $this->factory->create(MoneyType::class, $initial);

        static::assertSame($viewed, $form->createView()->vars['value']);

        $form->submit($submit);

        static::assertTrue($form->isSynchronized());

        $actual = $form->getData();
        static::assertInstanceOf(Money::class, $actual);
        static::assertTrue($expected->equals($actual));
    }

    public function valid(): Generator
    {
        yield [null, null, '4.6', Money::RUB(460)];
        yield [Money::RUB(50), '0,50', '4.6', Money::RUB(460)];
        yield [Money::RUB(100500), '1005,00', '4.6', Money::RUB(460)];
        yield [Money::RUB(100500), '1005,00', '4,6', Money::RUB(460)];
    }
}
