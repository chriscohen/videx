<?php

/**
 * @file
 * Simple wrapper class around a Goutte client that provides testable functionality that we need.
 */

declare(strict_types=1);

namespace ChrisCohen\Scraper;

use Goutte\Client;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;

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

    protected function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function getCrawler(): ?Crawler
    {
        return $this->crawler;
    }

    protected function setCrawler(Crawler $crawler): void
    {
        $this->crawler = $crawler;
    }

    public function scrape(): Response
    {
        // Perform the HTTP request.
        $crawler = $this->getClient()->request($this->getMethod(), $this->getUrl());
        $this->setCrawler($crawler);

        // Update the HTTP response. Needs type hinting due to ambiguous docs in BrowserKit.
        /** @var Response $response */
        $response = $this->getClient()->getResponse();
        $this->setResponse($response);

        return $response;
    }
}
