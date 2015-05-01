<?php

namespace MacDada\Wykop\AspieStats;

use Symfony\Component\DomCrawler\Crawler;
use UnexpectedValueException;

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
     * @throws UnexpectedValueException
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
            $this->extractCreatedAtDate($entry),
            $entry->filter('.description a')->attr('href'),
            $this->extractUser($entry)
        );
    }

    private function extractCreatedAtDate(Crawler $entry)
    {
        return new \DateTimeImmutable(
            $entry->filter('time[pubdate]')->attr('datetime')
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

        $cssClasses = explode(' ', $avatarImg->attr('class'));

        $isMale = in_array('male', $cssClasses);
        $isFemale = in_array('female', $cssClasses);

        if ($isMale && $isFemale) {
            throw new UnexpectedValueException('Only one gender class expected');
        }

        if ($isMale) {
            return User::GENDER_MALE;
        }

        if ($isFemale) {
            return User::GENDER_FEMALE;
        }

        throw new UnexpectedValueException('No gender given');
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
