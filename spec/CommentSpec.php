<?php

namespace spec\MacDada\Wykop\AspieStats;

use PhpSpec\ObjectBehavior;
use MacDada\Wykop\AspieStats\Comment;
use MacDada\Wykop\AspieStats\User;
use DateTimeImmutable;

class CommentSpec extends ObjectBehavior
{
    function it_is_initializable(User $user)
    {
        $this->beConstructedWith(1, new DateTimeImmutable(), 'http://wykop.pl', $user);

        $this->shouldHaveType('MacDada\Wykop\AspieStats\Comment');
    }

    function it_returns_its_data(DateTimeImmutable $createdAt)
    {
        $author = new User('m__b', User::GENDER_MALE, User::COLOR_BLACK);

        $this->beConstructedWith(1, $createdAt, 'http://wykop.pl', $author);

        $this->getId()->shouldReturn(1);
        $this->getCreatedAt()->shouldReturn($createdAt);
        $this->getSourceUrl()->shouldReturn('http://wykop.pl');

        $this->getAuthorUsername()->shouldReturn($author->getUsername());
        $this->getAuthorGender()->shouldReturn($author->getGender());
        $this->getAuthorColor()->shouldReturn($author->getColor());
    }

    function it_might_be_equal_to_other_comment()
    {
        $createdAt1 = new DateTimeImmutable();
        $createdAt2 = clone $createdAt1;
        $author1 = new User('m__b', User::GENDER_MALE, User::COLOR_BLACK);
        $author2 = new User('m__b', User::GENDER_MALE, User::COLOR_BLACK);

        $this->beConstructedWith(23, clone $createdAt1, 'http://wykop.pl', $author1);
        $otherComment = new Comment(23, $createdAt2, 'http://wykop.pl', $author2);

        $this->equals($otherComment)->shouldReturn(true);
    }

    function it_might_not_be_equal_to_other_comment()
    {
        $createdAt1 = new DateTimeImmutable();
        $createdAt2 = clone $createdAt1;
        $author1 = new User('m__b', User::GENDER_MALE, User::COLOR_BLACK);
        $author2 = new User('MacDada', User::GENDER_MALE, User::COLOR_BLACK);

        $this->beConstructedWith(23, clone $createdAt1, 'http://wykop.pl', $author1);
        $otherComment = new Comment(23, $createdAt2, 'http://wykop.pl', $author2);

        $this->equals($otherComment)->shouldReturn(false);
    }
}
