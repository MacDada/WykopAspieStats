<?php

namespace MacDada\Wykop\AspieStats;

class Comment
{
    private $id;
    private $createdAt;
    private $sourceUrl;
    private $author;

    public function __construct($id, \DateTimeImmutable $createdAt, $sourceUrl, User $author)
    {
        $this->id = (int) $id;
        $this->createdAt = $createdAt;
        $this->sourceUrl = (string) $sourceUrl;
        $this->author = $author;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    /**
     * @return string
     */
    public function getAuthorUsername()
    {
        return $this->author->getUsername();
    }

    /**
     * @return string
     */
    public function getAuthorGender()
    {
        return $this->author->getGender();
    }

    /**
     * @return string
     */
    public function getAuthorColor()
    {
        return $this->author->getColor();
    }

    /**
     * @param Comment $otherComment
     * @return bool
     */
    public function equals(Comment $otherComment)
    {
        return $otherComment->id === $this->id
            && $otherComment->createdAt == $this->createdAt
            && $otherComment->sourceUrl === $this->sourceUrl
            && $otherComment->author == $this->author;
    }
}
