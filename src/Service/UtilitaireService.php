<?php

namespace App\Service;

use App\Interface\DtoInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\ObjectMapper\ObjectMapper;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UtilitaireService
{

    public function __construct(
        private ValidatorInterface $validator,
        private SerializerInterface $serializer
    ) {
    }

    public function validate(DtoInterface $dto): ConstraintViolationListInterface|bool
    {
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }

            throw new \Exception($this->serializer->serialize($messages, 'json'));
        }

        return true;
    }

    public function mapAndValidateRequestDto($data, DtoInterface $object): DtoInterface {

        if (empty($data)) {
            throw  new Exception("Request is empty", 500);
        }

        $mapper = new ObjectMapper();

        $objectMapping = $mapper->map($data, $object);

        $this->validate($objectMapping);

        return $objectMapping;
    }
}
