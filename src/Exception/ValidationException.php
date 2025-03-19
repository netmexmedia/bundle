<?php

namespace Netmex\Bundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \RuntimeException
{
    private $violations;

    public function __construct(ConstraintViolationListInterface $violations, $message = "Invalid Request", $code = 422)
    {
        $this->violations = $violations;
        parent::__construct($message, $code);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}