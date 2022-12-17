<?php

namespace App\Controller\Traits;

use App\Controller\AbstractApiController;
use App\Controller\LoginController;
use InvalidArgumentException;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Form\FormInterface;
use App\Entity\Interfaces\SerializableEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolation;

trait SerializerAware
{
    /**
     * @var SerializerInterface|null
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     * @return self
     */
    private function setSerializer(SerializerInterface $serializer): self
    {
        $this->serializer = $serializer;
        return $this;
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

    private function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * Return the json string of the data, serialize for specifics groups
     * @param SerializableEntity $data
     */
    protected function serialize($data, array $group = ['Default']): string
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
    protected function serializeResponse(mixed $data, array $group = ['Default']): JsonResponse
    {
        $response = new JsonResponse([], JsonResponse::HTTP_OK);
        $json     = $this->serialize($data, $group);
        return $response->setJson($json);
    }

    /**
     * Simple JsonResponse use to transmit a message
     */
    protected function messageResponse(string $message, int $code = JsonResponse::HTTP_OK): JsonResponse
    {
        $response = new JsonResponse(
            [
                'code'    => $code,
                'message' => $message,
            ],
            $code
        );

        return $response;
    }

    /**
     * Simple JsonResponse use to transmit a message
     */
    protected function formErrorResponse(FormInterface $form, bool $showReason = true): JsonResponse
    {
        return new JsonResponse(
            [
                'code'    => Response::HTTP_NOT_ACCEPTABLE,
                'message' => 'Invalid form',
                'reason'  => $showReason ? $this->getFormErrorArray($form) : 'hidden',
            ],
            Response::HTTP_NOT_ACCEPTABLE
        );
    }

    private function getFormErrorArray(FormInterface $data): array
    {
        $form = $errors = [];

        foreach ($data->getErrors() as $error) {
            /** @var ConstraintViolation $cause */
            $cause = $error->getCause();
            $errors[$cause->getPropertyPath()] = $error->getMessage();
        }

        if ($errors) {
            $form['errors'] = $errors;
        }

        $children = [];
        foreach ($data->all() as $child) {
            if ($child instanceof FormInterface) {
                $children[$child->getName()] = $this->getFormErrorArray($child);
            }
        }

        if ($children) {
            $form['children'] = $children;
        }

        return $form;
    }

    /**
     * Simple JsonResponse use to transmit the new id of the created entity
     * @param mixed  $entity
     */
    protected function createResponse(SerializableEntity $entity, string $message): JsonResponse
    {
        if (!method_exists($entity, 'getId')) {
            throw new InvalidArgumentException('Entity must have a getId() method');
        }

        return new JsonResponse(
            [
                'code'    => JsonResponse::HTTP_CREATED,
                'message' => $message,
                'id'      => $entity->getIdentifier(),
                'entity'  => $this->serialize($entity),
            ],
            JsonResponse::HTTP_CREATED
        );
    }
}
