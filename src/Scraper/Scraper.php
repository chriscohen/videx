<?php

/**
 * @file
 * Simple wrapper class around a Goutte client that provides testable functionality that we need.
 */

declare(strict_types=1);

namespace ChrisCohen\Scraper;

use ChrisCohen\Model\Package;
use Goutte\Client;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\Exception\TransportException;

class Scraper
{

    /**
     * Goutte client.
     *
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $method;

    /**
     * The HTTP response from the scrape.
     *
     * @var Response
     */
    protected $response;

    /**
     * The main DOM-level crawler.
     *
     * @var Crawler
     */
    protected $crawler;

    /**
     * @var Package[]
     */
    protected $packages = [];

    /**
     * Scraper constructor.
     *
     * Instantiate a Goutte client.
     *
     * @param string $url
     *   The URL to be requested.
     * @param string $method
     *   The HTTP method to be used. Default is GET.
     */
    public function __construct($url, $method = 'GET')
    {
        $this->client = new Client();
        $this->url = $url;
        $this->method = $method;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function getCrawler(): ?Crawler
    {
        return $this->crawler;
    }

    public function setCrawler(Crawler $crawler): void
    {
        $this->crawler = $crawler;
    }

    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * Sort and return packages in descending price order.
     *
     * Note that this will not sort the internal $packages property on this class.
     *
     * @return Package[]
     */
    public function getPackagesByPrice(): array
    {
        // Copy of array to avoid changing the original.
        $packages = $this->getPackages();

        // Custom sort function within closure.
        usort($packages, function (Package $a, Package $b) {
            if ($a->getAnnualPrice() == $b->getAnnualPrice()) {
                return 0;
            }

            return ($a->getAnnualPrice() > $b->getAnnualPrice() ? -1 : 1);
        });

        return $packages;
    }

    /**
     * No array type checking in PHP yet, so we'll implement it ourselves.
     *
     * @param Package[] $packages
     *
     * @throws \InvalidArgumentException
     */
    public function setPackages(array $packages): void
    {
        foreach ($packages as $package) {
            if (!($package instanceof Package)) {
                throw new \InvalidArgumentException('Only arrays of Package entities may be passed to setPackages()');
            }
        }

        $this->packages = $packages;
    }

    public function addPackage(Package $package): void
    {
        $this->packages[] = $package;
    }

    public function clearPackages(): void
    {
        $this->packages = [];
    }

    public function scrape(): ?Response
    {
        // Perform the HTTP request.
        try {
            $crawler = $this->getClient()->request($this->getMethod(), $this->getUrl());
            $this->setCrawler($crawler);
        } catch (TransportException $e) {
            return null;
        }

        // Update the HTTP response. Needs type hinting due to ambiguous docs in BrowserKit.
        /** @var Response $response */
        $response = $this->getClient()->getResponse();
        $this->setResponse($response);

        return $response;
    }

    /**
     * Determine if the last scrape() succeeded.
     *
     * Note we are using only status code 200 to indicate a success. There are other 2xx codes that might be considered
     * a success, but for the purposes of this exercise, this will be sufficient.
     *
     * @return bool
     */
    public function succeeded(): bool
    {
        if (!empty($this->getResponse())) {
            return $this->getResponse()->getStatusCode() === 200;
        } else {
            return false;
        }
    }

    /**
     * @return Package[]|null
     */
    public function scrapePackages(): ?array
    {
        // Return null if the HTTP request has not yet been made.
        if (!$this->succeeded()) {
            return null;
        }

        $packages = [];

        // Loop through each "package" DOM element on the page and process it. Since we are using a closure here, we
        // will make sure $packages is available inside the closure so we can retrieve data from it.
        $this->getCrawler()->filter('div.package')->each(function ($element) use (&$packages) {
            $package = new Package();

            /** @var Crawler $element */
            $package->setTitle($this->getInnerContent($element, 'div.header > h3'));
            $package->setDescription($this->getInnerContent($element, 'div.package-name', 'html'));

            $price = $package->getPriceFromString($this->getInnerContent(
                $element,
                'div.package-price > span.price-big'
            ));

            // Check that $price is not null before we set a price for the package.
            if (!empty($price)) {
                $package->setPrice($price);
            }

            $discount = $package->getPriceFromString($this->getInnerContent($element, 'div.package-price > p'));

            // Check that $discount is not null before we set a discount on the package.
            if (!empty($discount)) {
                $package->setDiscount($discount);
            }

            $packages[] = $package;
        });

        $this->setPackages($packages);
        return $packages;
    }

    /**
     * Attempt to get the inner HTML or text from a specific filtered place within the input Crawler.
     *
     * @param Crawler $element
     * @param string $filter
     * @param string $method
     * @return string
     */
    public function getInnerContent(Crawler $element, string $filter, string $method = 'text'): string
    {
        try {
            $result = $element->filter($filter);

            switch ($method) {
                case 'text':
                    return $result->text();

                case 'html':
                    return $result->html();
            }
        } catch (\InvalidArgumentException $e) {
            return '';
        }
    }
}
