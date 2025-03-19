<?php

namespace Netmex\Bundle\EventListener;

use Netmex\Bundle\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ValidationExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ValidationException) {
            return;
        }

        $violations = $exception->getViolations();
        $errors = [];

        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        $response = new JsonResponse(
            data: [
                'message' => "Invalid request",
                'status' => $exception->getCode(),
                'errors' => $errors
            ], 
            status: 400
        );

        $event->setResponse($response);
    }
}
