<?php

namespace Netmex\Bundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \RuntimeException
{
    private $violations;

    public function __construct($message = "Invalid Request", $code = 422, ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
        parent::__construct($message, $code);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}