<?php

declare(strict_types=1);

namespace App\Tests\Form\Type;

use App\Form\Type\QuantityType;
use Generator;
use Symfony\Component\Form\Test\TypeTestCase;

final class QuantityTypeTest extends TypeTestCase
{
    /**
     * @dataProvider valid
     */
    public function testSubmitValidData(?int $initial, string $viewed, string $submit, int $expected): void
    {
        $form = $this->factory->create(QuantityType::class, $initial);

        self::assertSame($viewed, $form->createView()->vars['value']);

        $form->submit($submit);

        self::assertTrue($form->isSynchronized());
        self::assertSame($expected, $form->getData());
    }

    public function valid(): Generator
    {
        yield [null, '', '4.6', 460];
        yield [50, '0,50', '4.6', 460];
        yield [100500, '1005,00', '4.6', 460];
    }
}
