<?php

namespace spec\MacDada\Wykop\AspieStats;

use PhpSpec\ObjectBehavior;

use MacDada\Wykop\AspieStats\Comment;
use MacDada\Wykop\AspieStats\CommentRepository;

class InMemoryCommentRepositorySpec extends ObjectBehavior
{
    function it_is_a_comment_repository()
    {
        $this->shouldHaveType('MacDada\Wykop\AspieStats\InMemoryCommentRepository');
        $this->shouldImplement(CommentRepository::class);
    }

    function it_is_empty_by_default()
    {
        $this->countAll()->shouldReturn(0);
        $this->findAll()->shouldReturn([]);
    }

    function it_saves_a_comment(Comment $comment)
    {
        $this->save($comment);
        
        $this->countAll()->shouldReturn(1);
        $this->findAll()->shouldReturn([$comment]);
    }

    function it_saves_multiple_comments(Comment $comment1, Comment $comment2)
    {
        $this->save($comment1);
        $this->save($comment2);

        $this->countAll()->shouldReturn(2);
        $this->findAll()->shouldReturn([$comment1, $comment2]);
    }
}
