<?php

declare(strict_types=1);

namespace ChrisCohen\Tests;

use PHPUnit\Framework\TestCase;
use ChrisCohen\Model\Package;

final class PackageTest extends TestCase
{
    /**
     * @dataProvider formattingOfPricesData
     *
     * @param float $input
     *   The value to pass to setPrice() and setDiscount().
     * @param string $expected
     *   The value we expect to get back from formatPrice() and formatDiscount().
     */
    public function testFormattingOfPrices(float $input, string $expected)
    {
        $package = new Package();
        $package->setPrice($input);
        $package->setDiscount($input);

        $this->assertEquals($expected, $package->formatPrice());
        $this->assertEquals($expected, $package->formatDiscount());
    }

    public function formattingOfPricesData(): array
    {
        return [
            [0.0, '£0.00'],
            [10.0, '£10.00'],
            // Try something with too many decimals.
            [10.00001, '£10.00'],
            // Try a negative price.
            [-1.69, '-£1.69'],
        ];
    }
}
