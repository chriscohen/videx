<?php

declare(strict_types=1);

namespace ChrisCohen\Tests;

use ChrisCohen\Scraper\Scraper;
use PHPUnit\Framework\TestCase;
use stdClass;
use InvalidArgumentException;
use Symfony\Component\BrowserKit\Response;

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

    // Make sure we get null by calling Scraper->scrapePackages() before we actually downloaded the page.
    public function testScrapePackagesBeforeHttp()
    {
        $scraper = new Scraper('https://example.com');
        $this->assertEquals(null, $scraper->scrapePackages());
    }

    /**
     * Check that the succeeded() method gives the result we expect given different Response objects.
     *
     * @param int $statusCode
     * @param bool $expected
     *
     * @dataProvider succeededResponsesData
     */
    public function testSucceededResponses(int $statusCode, bool $expected)
    {
        $response = new Response('', $statusCode);
        $scraper = new Scraper('https://example.com');
        $scraper->setResponse($response);

        $this->assertEquals($expected, $scraper->succeeded());
    }

    public function succeededResponsesData(): array
    {
        return [
            [200, true],
            [206, false],
            [404, false],
            [418, false],
            [420, false],
            [500, false],
            [503, false],
            // Throw in some invalid response codes.
            [-999, false],
            [0, false],
            [99999999, false],
        ];
    }
}
