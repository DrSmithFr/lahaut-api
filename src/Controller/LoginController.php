<?php

declare(strict_types = 1);

namespace App\Controller;

use RuntimeException;
use App\Model\LoginModel;
use OpenApi\Annotations as OA;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Controller\Traits\SerializerAware;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    use SerializerAware;

    /**
     * ConnectionController constructor.
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->setSerializer($serializer);
    }

    /**
     * Initialise sessions with encryption API (Token valid for 30s)
     * @OA\Tag(name="Authentification")
     * @OA\RequestBody(@Model(type=LoginModel::class))
     * @OA\Response(
     *     response=200,
     *     description="User connected",
     *     @OA\Schema(
     *        type="object",
     *        example={"token": "gjc7834ace3-8525-4814-bf0f-b7146bc9e8ab"}
     *     )
     * )
     */
    #[Route(path: '/login', name: 'app_login', methods: ['post'])]
    final public function login(): never
    {
        throw new RuntimeException(
            'You may have screwed the firewall configuration, this function should not have been called.'
        );
    }
}
