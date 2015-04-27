<?php

namespace spec\MacDada\Wykop\AspieStats;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\BrowserKit\Client;

class FetcherSpec extends ObjectBehavior
{
    function let(Client $client, Logger $logger)
    {
        $this->beConstructedWith($client, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('MacDada\Wykop\AspieStats\Fetcher');
    }

    function it_crawls_first_page(Client $client, Logger $logger)
    {
        $logger->info('Fetching…')->shouldBeCalled();


        $pageUrl = 'http://www.wykop.pl/tag/aspiequiz/wszystkie/';
        $logger->info('Downloading '.$pageUrl)->shouldBeCalled();
        $client->request('GET', $pageUrl)->shouldBeCalled();
        $logger->debug('Downloaded '.$pageUrl)->shouldBeCalled();

        $this->fetch();
    }
}
