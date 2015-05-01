<?php

namespace MacDada\Wykop\AspieStats;

interface CommentRepository
{
    /**
     * @return Comment[]
     */
    public function findAll();

    /**
     * @return int
     */
    public function countAll();

    /**
     * @param Comment $comment
     */
    public function save(Comment $comment);
}
