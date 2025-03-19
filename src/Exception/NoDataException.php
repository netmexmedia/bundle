<?php

namespace Netmex\Bundle\Exception;

class NoDataException extends \RuntimeException
{
    public function __construct($message = "No data provided", $code = 422)
    {
        parent::__construct($message, $code);
    }
}