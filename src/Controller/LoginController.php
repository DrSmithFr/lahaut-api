<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\LoginModel;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use RuntimeException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Authentification")
 */
#[Security(name: null)]
class LoginController extends AbstractApiController
{
    /**
     * Initialise sessions with encryption API (Token valid for 30s)
     * @OA\RequestBody(@Model(type=LoginModel::class))
     * @OA\Response(
     *     response=200,
     *     description="User connected",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      @OA\Schema(type="object", example={"token": "gjc7834ace3-8525-4814-bf0f-b7146bc9e8ab"})
     *    )
     * )
     * @OA\Response(
     *     response="401",
     *     description="Cannot connect user"
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
