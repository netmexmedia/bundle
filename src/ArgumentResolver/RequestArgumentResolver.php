<?php

namespace Netmex\Bundle\ArgumentResolver;

use Netmex\Bundle\Exception\InvalidDataException;
use Netmex\Bundle\Exception\InvalidJsonException;
use Netmex\Bundle\Exception\NoDataException;
use Netmex\Bundle\Exception\ValidationException;
use Netmex\Bundle\Request\FormRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestArgumentResolver implements ValueResolverInterface
{
    private ValidatorInterface $validator;

    private SerializerInterface $serializer;

    public string $type;

    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $this->type = $argument->getType();

        if(!is_a($this->type, FormRequestInterface::class, true))
        {
            return;
        }

        $content = $request->getContent();

        $this->validateJson($content);
        $this->checkEmptyData($content);
        $this->checkExtraFields($content);

        $dto = $this->validateFields($content);

        yield $dto;
    }

    public function checkEmptyData($content)
    {
        if (empty($content)) {
            $errorMsg = implode(", ", $this->getProperties());
            throw new NoDataException("No data provided: Expected fields [ $errorMsg ]");
        }
    }

    public function validateJson($data)
    {
        $data = json_decode($data, true);

        if($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $errorMsg = json_last_error_msg();
            throw new InvalidJsonException("Invalid JSON format: $errorMsg. Please ensure the JSON structure is correct.");
        }
    }

    public function checkExtraFields($content)
    {
        $data = json_decode($content, true);
        $extraProperties = array_diff(
            array_keys($data), 
            $this->getProperties()
        );
        if ($extraProperties) {
            $errorMsg = implode(', ', $extraProperties);
            $available = implode(", ", $this->getProperties());
            throw new InvalidDataException("Invalid field(s) provided: [ $errorMsg ]. \n Expected fields: [ $available ]");
        }
    }

    public function validateFields($content): FormRequestInterface
    {
        $dto = $this->serializer->deserialize($content, $this->type, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $error = $errors->get(0);
            $name = $error->getPropertyPath();
            $errorMsg = $error->getMessage();

            throw new ValidationException($errors, "Invalid Request for $name: $errorMsg", 422);
        }

        return $dto;
    }

    public function getProperties(): array
    {
        $reflectionClass = new \ReflectionClass($this->type);
        return array_map(
            fn($property) => $property->getName(), 
            $reflectionClass->getProperties()
        );
    }
}