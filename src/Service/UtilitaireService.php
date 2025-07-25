<?php

namespace App\Service;

use App\Event\EmailEvent;
use App\Interface\DtoInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\ObjectMapper\ObjectMapper;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UtilitaireService
{

    public function __construct(
        private ValidatorInterface  $validator,
        private SerializerInterface $serializer,
        private EventDispatcherInterface $eventDispatcher
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

    public function sendEmail($subject, $to, $template, $context) {
        $email = new EmailEvent($subject, $to, $template, $context);

        $this->eventDispatcher->dispatch($email);
    }
}
