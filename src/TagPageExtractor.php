<?php

namespace MacDada\Wykop\AspieStats;

use Symfony\Component\DomCrawler\Crawler;

class TagPageExtractor
{
    /**
     * @param Crawler $pageCrawler
     * @return Comment[]
     */
    public function extract(Crawler $pageCrawler)
    {
        $entryCrawler = $pageCrawler->filter('#itemsStream .entry p.description[data-type="entry"]');

        return $entryCrawler->each(function (Crawler $description) {
            return new Comment(
                $description->attr('data-id'),
                new \DateTime(),
                $description->filter('a')->attr('href'),
                new User('m__b', User::GENDER_MALE, User::COLOR_BLACK)
            );
        });
    }
}
