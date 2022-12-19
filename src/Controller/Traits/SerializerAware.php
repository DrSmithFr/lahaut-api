<?php

namespace App\Controller\Traits;

use App\Entity\Interfaces\Serializable;
use App\Entity\Interfaces\SerializableEntity;
use App\Model\FormErrorDetailModel;
use App\Model\FormErrorModel;
use InvalidArgumentException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;

trait SerializerAware
{
    /**
     * @var SerializerInterface|null
     */
    private ?SerializerInterface $serializer;

    private function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    private function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * Create serialization context for specifics groups
     * with serialize null field enable
     */
    private function getSerializationContext(array $group = ['Default']): SerializationContext
    {
        $context = SerializationContext::create();
        $context->setSerializeNull(true);
        $context->setGroups($group);
        return $context;
    }

    /**
     * Return the array version of the data, serialize for specifics groups
     */
    protected function toArray(Serializable $data, array $group = ['Default']): array
    {
        $builder = SerializerBuilder::create();

        return $builder
            ->build()
            ->toArray($data, $this->getSerializationContext($group));
    }

    /**
     * Return the json string of the data, serialize for specifics groups
     */
    protected function serialize(Serializable $data, array $group = ['Default']): string
    {
        return $this
            ->getSerializer()
            ->serialize(
                $data,
                'json',
                $this->getSerializationContext($group)
            );
    }

    /**
     * Return the JsonResponse of the data, serialize for specifics groups
     */
    protected function serializeResponse(
        Serializable $data,
        array        $group = ['Default'],
        int          $status = Response::HTTP_OK
    ): JsonResponse
    {
        $response = new JsonResponse([], $status);
        $json = $this->serialize($data, $group);
        return $response->setJson($json);
    }

    /**
     * Simple JsonResponse use to transmit a message
     */
    protected function messageResponse(string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(
            [
                'message' => $message,
            ],
            $status
        );
    }

    /**
     * Simple JsonResponse use to transmit a message
     */
    protected function formErrorResponse(
        FormInterface $form,
        int           $status
    ): JsonResponse
    {
        $formError = (new FormErrorModel())
            ->setCode($status)
            ->setReason($this->getFormErrorDetail($form));
        return new JsonResponse(
            $this->toArray($formError),
            $status
        );
    }

    private function getFormErrorDetail(FormInterface $data): FormErrorDetailModel
    {
        $reason = new FormErrorDetailModel();
        $errors = $children = [];

        foreach ($data->getErrors() as $error) {
            /** @var ConstraintViolation $cause */
            $cause = $error->getCause();
            $errors[$cause->getPropertyPath()] = $error->getMessage();
        }

        $reason->setErrors($errors);

        foreach ($data->all() as $child) {
            if ($child instanceof FormInterface) {
                $children[$child->getName()] = $this->getFormErrorDetail($child);
            }
        }

        $reason->setChildren($children);

        return $reason;
    }

    /**
     * Simple JsonResponse use to transmit the new identifier of the created entity
     */
    protected function createResponse(
        SerializableEntity $entity,
        string             $message,
        int                $status = Response::HTTP_CREATED
    ): JsonResponse
    {
        if (!method_exists($entity, 'getId')) {
            throw new InvalidArgumentException('Entity must have a getId() method');
        }

        return new JsonResponse(
            [
                'code' => $status,
                'message' => $message,
                'id' => $entity->getIdentifier(),
                'entity' => $this->toArray($entity),
            ],
            $status
        );
    }
}
