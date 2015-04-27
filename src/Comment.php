<?php

namespace MacDada\Wykop\AspieStats;

class Comment
{
    private $id;
    private $createdAt;
    private $sourceUrl;
    private $author;

    public function __construct($id, \DateTime $createdAt, $sourceUrl, User $author)
    {
        if (empty($sourceUrl)) {
            throw new \InvalidArgumentException('Empty source url');
        }

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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    public function getAuthorUsername()
    {
        return $this->author->getUsername();
    }

    public function getAuthorGender()
    {
        return $this->author->getGender();
    }

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
