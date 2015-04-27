<?php

namespace spec\MacDada\Wykop\AspieStats;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DomCrawler\Crawler;
use MacDada\Wykop\AspieStats\Comment;
use MacDada\Wykop\AspieStats\User;
use DateTime;
use MacDada\Wykop\AspieStats\TagPageExtractor;

class TagPageExtractorSpec extends ObjectBehavior
{
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
                new DateTime(),
                'http://wykop.pl/123',
                new User('m__b', User::GENDER_MALE, User::COLOR_BLACK)
            ),
            // todo: zmienić dane usera
            new Comment(
                321,
                new DateTime(),
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

        $this->extract($crawler)->shouldBeLike($comments);
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
                        <p class="description">
                            Źródło:
                            <a href="'.$comment->getSourceUrl().'">Some source</a>
                        </p>
                    </div>
                </li>
            ';
        }, $comments));
    }

    private function getAuthorColorCssClass(Comment $comment)
    {
        return array_flip(TagPageExtractor::COLORS)[$comment->getAuthorColor()];
    }
}
