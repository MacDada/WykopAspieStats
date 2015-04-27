<?php

namespace MacDada\Wykop\AspieStats;

use Symfony\Component\DomCrawler\Crawler;

class TagPageExtractor
{
    const COLORS = [
        'color-5' => User::COLOR_BLACK,
        'color-1' => User::COLOR_ORANGE,
        'color-0' => User::COLOR_GREEN,
    ];

    /**
     * @param Crawler $pageCrawler
     * @return Comment[]
     */
    public function extract(Crawler $pageCrawler)
    {
        $entryCrawler = $pageCrawler->filter('#itemsStream .entry div.dC[data-type="entry"]');

        return $entryCrawler->each(function (Crawler $entry) {
            return $this->extractComment($entry);
        });
    }

    private function extractComment(Crawler $entry)
    {
        return new Comment(
            $entry->attr('data-id'),
            new \DateTime(),
            $entry->filter('.description a')->attr('href'),
            $this->extractUser($entry)
        );
    }

    private function extractUser(Crawler $entry)
    {
        $showProfileSummary = $entry->filter('.showProfileSummary');

        return new User(
            $showProfileSummary->filter('b')->text(),
            $this->extractGender($entry),
            $this->extractColor($showProfileSummary)
        );
    }

    private function extractGender(Crawler $entry)
    {
        $avatarImg = $entry->filter('a.profile img.avatar');

        if (false !== strpos($avatarImg->attr('class'), 'male')) {
            return User::GENDER_MALE;
        }

        if (false !== strpos($avatarImg->attr('class'), 'female')) {
            return User::GENDER_FEMALE;
        }
    }

    private function extractColor(Crawler $showProfileSummary)
    {
        foreach (static::COLORS as $colorClass => $colorValue) {
            if (false !== strpos($showProfileSummary->attr('class'), $colorClass)) {
                return $colorValue;
            }
        }
    }
}
