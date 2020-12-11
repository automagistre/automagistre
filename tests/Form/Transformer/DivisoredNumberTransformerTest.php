<?php

declare(strict_types=1);

namespace App\Tests\Form\Transformer;

use App\Form\Transformer\DivisoredNumberTransformer;
use Generator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class DivisoredNumberTransformerTest extends TestCase
{
    /**
     * @dataProvider transform
     */
    public function testTransform(int $divisor, ?int $value, ?string $expected): void
    {
        static::assertSame($expected, (new DivisoredNumberTransformer($divisor))->transform($value));
    }

    public function transform(): Generator
    {
        yield [100, null, null];
        yield [100, 100, '1,00'];
        yield [100, 1000, '10,00'];
    }

    /**
     * @dataProvider reverseTransform
     *
     * @param int|float $value
     */
    public function testReverseTransform(int $divisor, $value, ?int $expected): void
    {
        static::assertSame($expected, (new DivisoredNumberTransformer($divisor))->reverseTransform($value));
    }

    public function reverseTransform(): Generator
    {
        yield [100, null, null];
        yield [100, '4.6', 460];
        yield [100, '4,6', 460];
        yield [100, 4.6, 460];
        yield [1000, '4.6', 4600];
        yield [1000, 4.6, 4600];
        yield [10000, '4.64', 46400];
        yield [10000, '4,64', 46400];
        yield [10000, 4.64, 46400];
    }

    /**
     * @dataProvider transformFail
     *
     * @param mixed $value
     */
    public function testTransformFail($value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Integer expected.');

        (new DivisoredNumberTransformer(1))->transform($value);
    }

    public function transformFail(): Generator
    {
        yield [4.6];
        yield ['bla'];
        yield [''];
        yield [new stdClass()];
    }

    /**
     * @dataProvider reverseTransformFail
     *
     * @param int|float $value
     */
    public function testReverseTransformFail($value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Numeric expected.');

        (new DivisoredNumberTransformer(1))->reverseTransform($value);
    }

    public function reverseTransformFail(): Generator
    {
        yield ['bla'];
        yield [new stdClass()];
    }
}
