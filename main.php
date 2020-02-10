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
