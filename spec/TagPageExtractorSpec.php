<?php

namespace spec\MacDada\Wykop\AspieStats;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DomCrawler\Crawler;
use MacDada\Wykop\AspieStats\Comment;
use MacDada\Wykop\AspieStats\User;
use DateTimeImmutable;
use MacDada\Wykop\AspieStats\TagPageExtractor;
use Psr\Log\LoggerInterface;

class TagPageExtractorSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('MacDada\Wykop\AspieStats\TagPageExtractor');
    }

    function it_returns_empty_array_when_no_comments_found()
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent('
            <div id="content">
                <h1>Wykop</h1>
                <p>Brak komentarzy</b>
            </div>
        ');

        $this->extract($crawler)->shouldReturn([]);
    }

    function it_returns_found_comments()
    {
        $comments = [
            new Comment(
                123,
                new DateTimeImmutable('-1 h'),
                'http://wykop.pl/123',
                new User('m__b', User::GENDER_MALE, User::COLOR_BLACK)
            ),
            new Comment(
                321,
                new DateTimeImmutable('2015-02-03T21:22:23+02:00'),
                'http://wykop.pl/321',
                new User('MacDada', User::GENDER_MALE, User::COLOR_ORANGE)
            )
        ];

        $crawler = new Crawler();
        $crawler->addHtmlContent('
            <div id="content">
                <ul id="itemsStream">
                    '.$this->renderComments($comments).'
                </ul>
            </div>
        ');

        $foundComments = $this->extract($crawler);

        $foundComments->shouldHaveCount(2);
        $foundComments->shouldBeLike($comments);
    }

    function it_expects_a_gender_css_class()
    {
        $crawler = $this->createCrawlerWithCustomGender('');

        $this
            ->shouldThrow(new \UnexpectedValueException('No gender given'))
            ->duringExtract($crawler);
    }

    function it_expects_only_one_type_of_genders()
    {
        $crawler = $this->createCrawlerWithCustomGender('male female');

        $this
            ->shouldThrow(new \UnexpectedValueException('Only one gender class expected'))
            ->duringExtract($crawler);
    }

    function it_expects_valid_gender_css_class()
    {
        $crawler = $this->createCrawlerWithCustomGender('male_trololo abc');

        $this
            ->shouldThrow(new \UnexpectedValueException('No gender given'))
            ->duringExtract($crawler);
    }

    function it_expects_a_user_color_class()
    {
        $crawler = $this->createCrawlerWithCustomUserColorClass('');

        $this
            ->shouldThrow(new \UnexpectedValueException('No user color given'))
            ->duringExtract($crawler);
    }

    function it_expects_only_one_type_of_colors()
    {
        $crawler = $this->createCrawlerWithCustomUserColorClass('color-0 color-1');

        $this
            ->shouldThrow(new \UnexpectedValueException('Only one user color class expected'))
            ->duringExtract($crawler);
    }

    function it_expects_valid_user_color_css_class()
    {
        $crawler = $this->createCrawlerWithCustomUserColorClass('ddcolor-0');

        $this
            ->shouldThrow(new \UnexpectedValueException('No user color given'))
            ->duringExtract($crawler);
    }

    function it_parses_comments_without_source_url()
    {
        $comment = new Comment(
            100,
            new \DateTimeImmutable(),
            '',
            new User('elfik', User::GENDER_FEMALE, User::COLOR_BLUE)
        );

        $crawler = new Crawler();
        $crawler->addHtmlContent('
            <div id="content">
                <ul id="itemsStream">
                    '.$this->renderComments([$comment]).'
                </ul>
            </div>
        ');

        $foundComments = $this->extract($crawler);

        $foundComments->shouldHaveCount(1);
        $foundComments[0]->shouldBeLike($comment);
    }

    function it_extracts_created_at_date()
    {
        $createdAt = new DateTimeImmutable('2015-04-27T22:42:53+02:00');

        $crawler = new Crawler();
        $crawler->addHtmlContent('
            <div id="content">
                <ul id="itemsStream">
                    <li class="entry">
                        <div class="dC" data-type="entry" data-id="123">
                            <a class="profile">
                                <img class="avatar male" />
                            </a>
                            <a class="showProfileSummary color-0">
                                <b>m__b</b>
                            </a>
                            <time datetime="'.$createdAt->format(DATE_W3C).'" pubdate />
                            <p class="description">
                                Źródło:
                                <a href="#">Some source</a>
                            </p>
                        </div>
                        '.$this->renderSubcomments().'
                    </li>
                </ul>
            </div>
        ');

        $foundComments = $this->extract($crawler);

        $foundComments->shouldHaveCount(1);
        $foundComments[0]->shouldHaveType(Comment::class);
        $foundComments[0]->getCreatedAt()->shouldBeLike($createdAt);
    }

    private function createCrawlerWithCustomGender($gender)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent('
            <div id="content">
                <ul id="itemsStream">
                    <li class="entry">
                        <div class="dC" data-type="entry" data-id="123">
                            <a class="profile">
                                <img class="avatar '.$gender.'" />
                            </a>
                            <a class="showProfileSummary color-0">
                                <b>m__b</b>
                            </a>
                            <time datetime="2015-04-27T22:42:53+02:00" pubdate />
                            <p class="description">
                                Źródło:
                                <a href="#">Some source</a>
                            </p>
                        </div>
                        '.$this->renderSubcomments().'
                    </li>
                </ul>
            </div>
        ');

        return $crawler;
    }

    private function createCrawlerWithCustomUserColorClass($color)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent('
            <div id="content">
                <ul id="itemsStream">
                    <li class="entry">
                        <div class="dC" data-type="entry" data-id="123">
                            <a class="profile">
                                <img class="avatar male" />
                            </a>
                            <a class="showProfileSummary '.$color.'">
                                <b>m__b</b>
                            </a>
                            <time datetime="2015-04-27T22:42:53+02:00" pubdate />
                            <p class="description">
                                Źródło:
                                <a href="#">Some source</a>
                            </p>
                        </div>
                        '.$this->renderSubcomments().'
                    </li>
                </ul>
            </div>
        ');

        return $crawler;
    }

    /**
     * @param array $comments
     * @return string
     */
    private function renderComments(array $comments)
    {
        return join('', array_map(function (Comment $comment) {
            return '
                <li class="entry">
                    <div class="dC" data-type="entry" data-id="'.$comment->getId().'">
                        <a class="profile">
                            <img class="avatar '.$comment->getAuthorGender().'" />
                        </a>
                        <a class="showProfileSummary '.$this->getAuthorColorCssClass($comment).'">
                            <b>'.$comment->getAuthorUsername().'</b>
                        </a>
                        <time datetime="'.$comment->getCreatedAt()->format(DATE_W3C).'" pubdate />
                        '.$this->renderSource($comment).'
                    </div>
                    '.$this->renderSubcomments().'
                </li>
            ';
        }, $comments));
    }

    private function renderSource(Comment $comment)
    {
        if (empty($comment->getSourceUrl())) {
            return '';
        }

        return '
            <p class="description">
                Źródło:
                <a href="'.$comment->getSourceUrl().'">Some source</a>
            </p>
        ';
    }

    private function renderSubcomments()
    {
        return '
            <ul class="sub">
                <li>
                    <div class="dC" data-type="entrycomment" data-id="12345">
                        <a class="profile">
                             <img class="avatar male" />
                        </a>
                        <a class="showProfileSummary color-1">
                            <b>m__b</b>
                        </a>
                        <time datetime="2015-04-27T22:42:53+02:00" pubdate />
                        <p class="description">
                            Źródło:
                            <a href="#">Some source</a>
                        </p>
                    </div>
                </li>
            </ul>
        ';
    }

    private function getAuthorColorCssClass(Comment $comment)
    {
        return array_flip(TagPageExtractor::COLORS)[$comment->getAuthorColor()];
    }
}
