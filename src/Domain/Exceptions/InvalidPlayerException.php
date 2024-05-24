<?php

namespace Domain\Exceptions;

use Exception;

class InvalidPlayerException extends Exception
{
    public static function playerNotFound(): self
    {
        return new self('The given player not found');
    }
}
