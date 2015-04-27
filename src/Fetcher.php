<?php

namespace MacDada\Wykop\AspieStats;

use Symfony\Component\BrowserKit\Client;
use Psr\Log\LoggerInterface;

class Fetcher
{
    const FIRST_PAGE_URL = 'http://www.wykop.pl/tag/aspiequiz/wszystkie/';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function fetch()
    {
        $this->logger->info('Fetching…');

        $this->logger->info('Downloading '.static::FIRST_PAGE_URL);
        $this->client->request('GET', static::FIRST_PAGE_URL);
        $this->logger->debug('Downloaded '.static::FIRST_PAGE_URL);
    }
}
