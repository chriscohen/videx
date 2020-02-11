<?php

declare(strict_types=1);

namespace ChrisCohen\Tests;

use ChrisCohen\Scraper\Scraper;
use PHPUnit\Framework\TestCase;
use stdClass;
use InvalidArgumentException;

final class ScraperTest extends TestCase
{
    /**
     * Test that we can't use Scraper->setPackages() unless we're only passing an array of Package entities.
     */
    public function testSetInvalidPackage()
    {
        $this->expectException(InvalidArgumentException::class);
        $scraper = new Scraper('');

        // Set up an array that contains something that's NOT a Package, and try to setPackages().
        $wrong = [new stdClass()];
        $scraper->setPackages($wrong);
    }

    /**
     * Attempt to scrape nonsense URLs and make sure that the Scraper->succeeded() method is returning false.
     *
     * @param string $url
     *   The "URL" we will use to instantiate the Scraper.
     * @param bool $expected
     *   Whether or not we expect the Scraper->succeded() method to be true when we scrape.
     *
     * @dataProvider succeededData
     */
    public function testSucceeded(string $url, bool $expected)
    {
        $scraper = new Scraper($url);
        $scraper->scrape();
        $this->assertEquals($expected, $scraper->succeeded());
    }

    public function succeededData(): array
    {
        return [
            ['fakeurl', false],
            ['"£$^%$^$', false],
        ];
    }
}
