<?php

namespace Netmex\Bundle\Exception;

class InvalidDataException extends \RuntimeException
{
    public function __construct($message = "Invalid JSON format", $code = 422)
    {
        parent::__construct($message, $code);
    }
}