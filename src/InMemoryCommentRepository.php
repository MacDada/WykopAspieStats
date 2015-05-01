<?php

namespace MacDada\Wykop\AspieStats;

class InMemoryCommentRepository implements CommentRepository
{
    /**
     * @var Comment[]
     */
    private $comments = [];

    /**
     * @return Comment[]
     */
    public function findAll()
    {
        return $this->comments;
    }

    /**
     * @return int
     */
    public function countAll()
    {
        return count($this->comments);
    }

    /**
     * @param Comment $comment
     */
    public function save(Comment $comment)
    {
        $this->comments[] = $comment;
    }
}
