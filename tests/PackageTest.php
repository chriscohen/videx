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

    /**
     * Test that the replaceBrs() method is... replacing <br> and <br/> properly.
     *
     * @param string $input
     * @param string $expected
     *
     * @dataProvider replaceBrsData
     */
    public function testReplaceBrs(string $input, string $expected)
    {
        $package = new Package();
        $package->setDescription($input);

        $this->assertEquals($expected, $package->getDescription());
    }

    public function replaceBrsData(): array
    {
        return [
            ['<br>', ' '],
            ['The quick brown fox<br>tripped over the lazy dog.', 'The quick brown fox tripped over the lazy dog.'],
            ['<br/>', ' '],
            ['The quick brown fox<br/>told off the lazy dog.', 'The quick brown fox told off the lazy dog.'],
            // Test a string with both <br> and <br/>
            [
                'The lazy dog<br>told the quick brown fox<br/>to look where he was going.',
                'The lazy dog told the quick brown fox to look where he was going.'
            ],
            // Test something with nothing but <br>s.
            ['<br><br><br/><br/>', '    '],
            // Test something with no <br>s.
            ['The fox apologised and they went to the pub.', 'The fox apologised and they went to the pub.'],
        ];
    }
}
