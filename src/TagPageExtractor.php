<?php

namespace MacDada\Wykop\AspieStats;

use Symfony\Component\DomCrawler\Crawler;
use UnexpectedValueException;
use Psr\Log\LoggerInterface;

class TagPageExtractor
{
    const COLORS = [
        'color-5' => User::COLOR_BLACK,
        'color-2001' => User::COLOR_BLUE,
        'color-1001' => User::COLOR_SILVER,
        'color-2' => User::COLOR_MAROON,
        'color-1' => User::COLOR_ORANGE,
        'color-0' => User::COLOR_GREEN,
    ];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Crawler $pageCrawler
     * @return Comment[]
     * @throws UnexpectedValueException
     */
    public function extract(Crawler $pageCrawler)
    {
        $entryCrawler = $pageCrawler->filter('#itemsStream .entry div.dC[data-type="entry"]');

        $found = [];

        $entryCrawler->each(function (Crawler $entry) use (&$found) {
            $comment = $this->extractComment($entry);

            if (null !== $comment) {
                $found[] = $comment;
            }
        });

        return $found;
    }

    /**
     * @param Crawler $entry
     * @return Comment|null
     */
    private function extractComment(Crawler $entry)
    {
        $sourceUrl = $this->sourceUrl($entry);
        $commentId = (int) $entry->attr('data-id');

        if (null === $sourceUrl) {
            $this->logger->info(
                'TagPageExtractor: skipping comment without source',
                ['commentId' => $commentId]
            );

            return null;
        }

        return new Comment(
            $commentId,
            $this->extractCreatedAtDate($entry),
            $sourceUrl,
            $this->extractUser($entry)
        );
    }

    /**
     * @param Crawler $entry
     * @return string|null
     */
    private function sourceUrl(Crawler $entry)
    {
        $sourceLink = $entry->filter('.description a');

        return $sourceLink->count() ? $sourceLink->attr('href') : null;
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
        $found = [];

        $cssClasses = explode(' ', $showProfileSummary->attr('class'));

        foreach (static::COLORS as $colorClass => $colorValue) {
            if (in_array($colorClass, $cssClasses)) {
                $found[] = $colorValue;
            }
        }

        if (empty($found)) {
            throw new UnexpectedValueException('No user color given');
        }

        if (count($found) > 1) {
            throw new UnexpectedValueException('Only one user color class expected');
        }

        return $found[0];
    }
}
