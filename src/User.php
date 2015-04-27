<?php

namespace MacDada\Wykop\AspieStats;

use InvalidArgumentException;

class User
{
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    const COLOR_BLACK = 'black';
    const COLOR_BLUE = 'blue';
    const COLOR_SILVER = 'silver';

    const COLOR_MAROON = 'maroon';
    const COLOR_ORANGE = 'orange';
    const COLOR_GREEN = 'green';

    private $username;
    private $gender;
    private $color;

    /**
     * @param string $username
     * @param string $gender
     * @param string $color
     * @throws InvalidArgumentException
     */
    public function __construct($username, $gender, $color)
    {
        $this->setUsername($username);
        $this->setGender($gender);
        $this->setColor($color);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param User $otherUser
     * @return bool
     */
    public function equals(User $otherUser)
    {
        return $otherUser->getUsername() === $this->username
            && $otherUser->getGender() === $this->gender
            && $otherUser->getColor() === $this->color;
    }

    private function setUsername($username)
    {
        if (empty($username)) {
            throw new InvalidArgumentException('Empty username');
        }

        $this->username = (string) $username;
    }

    private function setGender($gender)
    {
        if (!in_array($gender, $this->getGenders())) {
            throw new InvalidArgumentException(sprintf('Invalid gender: "%s"', $gender));
        }

        $this->gender = $gender;
    }

    private function setColor($color)
    {
        if (!in_array($color, $this->getColors())) {
            throw new InvalidArgumentException(sprintf('Invalid color: "%s"', $color));
        }

        $this->color = $color;
    }

    private function getGenders()
    {
        return [
            static::GENDER_MALE,
            static::GENDER_FEMALE,
        ];
    }

    private function getColors()
    {
        return [
            static::COLOR_BLACK,
            static::COLOR_BLUE,
            static::COLOR_SILVER,
            static::COLOR_MAROON,
            static::COLOR_ORANGE,
            static::COLOR_GREEN,
        ];
    }
}
