<?php

namespace App\Controller\Api;

use App\Controller\AbstractApiController;
use App\Model\ApiVersionModel;
use App\Service\ApiVersionService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="API")
 */
class ApiController extends AbstractApiController
{
    /**
     * Retrieve API version
     * @OA\Response(
     *     response=200,
     *     description="User created",
     *     @Model(type=ApiVersionModel::class)
     * )
     */
    #[Route(
        path: '/public/version',
        name: 'app_api_version',
        methods: ['get']
    )]
    public function listAllActivityTypes(
        ApiVersionService $apiVersionService,
    ): JsonResponse {
        return $this->serializeResponse($apiVersionService->getVersion());
    }
}
