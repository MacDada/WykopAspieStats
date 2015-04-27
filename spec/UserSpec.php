<?php

namespace spec\MacDada\Wykop\AspieStats;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use InvalidArgumentException;
use MacDada\Wykop\AspieStats\User;

class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('m__b', 'male', 'black');
        $this->shouldHaveType('MacDada\Wykop\AspieStats\User');
    }

    function it_throws_exception_on_empty_username()
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->during__construct('', 'male', 'black');
    }

    function it_throws_exception_on_invalid_gender()
    {
        $this
            ->shouldThrow(new InvalidArgumentException(
                'Invalid gender: "unknown_gender"'
            ))
            ->during__construct('m__b', 'unknown_gender', 'black');
    }

    function it_throws_exception_on_invalid_color()
    {
        $this
            ->shouldThrow(new InvalidArgumentException(
                'Invalid color: "unknown_color"'
            ))
            ->during__construct('m__b', 'male', 'unknown_color');
    }

    function it_returns_its_data()
    {
        $this->beConstructedWith('m__b', 'male', 'black');

        $this->getUsername()->shouldReturn('m__b');
        $this->getGender()->shouldReturn('male');
        $this->getColor()->shouldReturn('black');
    }

    function it_might_be_equal_to_other_user()
    {
        $this->beConstructedWith('m__b', 'male', 'black');

        $otherUser = new User('m__b', 'male', 'black');

        $this->equals($otherUser)->shouldReturn(true);
    }

    function it_might_not_be_equal_to_other_user(User $otherUser)
    {
        $this->beConstructedWith('m__b', 'male', 'black');

        $this->equals($otherUser)->shouldReturn(false);
    }
}
