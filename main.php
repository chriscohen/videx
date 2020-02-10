<?php

/**
 * @file
 * Main runtime for Videx scraper test.
 */

declare(strict_types=1);

use ChrisCohen\Scraper\Scraper;

// Use composer's autoloader. We use require rather than require_once to avoid the performance hit of require_once when
// we know we are only calling it once.
require 'vendor/autoload.php';

$scraper = new Scraper('https://videx.comesconnected.com');
$scraper->scrape();

// Check for a failure in the HTTP request.
if (!$scraper->succeeded()) {
    $code = $scraper->getResponse()->getStatusCode();
    throw new HttpException(sprintf(
        'Could not scrape %s - received status code %d',
        $scraper->getUrl(),
        $code
    ));
}
