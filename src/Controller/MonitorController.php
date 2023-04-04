<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\RoleEnum;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Monitors Management")
 */
#[Security(name: null)]
class MonitorController extends AbstractApiController
{
    /**
     * @OA\Response(
     *     response=201,
     *     description="Monitor information",
     *     @Model(type=User::class)
     * )
     * @OA\Response(response="404", description="No monitor found")
     */
    #[Route(
        path: '/public/monitor/{uuid}',
        name: 'app_monitor_information',
        requirements: ['id' => '\d+'],
        methods: ['get']
    )]
    public function currentUser(
        #[MapEntity(class: User::class)] User $user,
    ): JsonResponse {
        if (!$user->hasRole(RoleEnum::MONITOR)) {
            return $this->messageResponse('Monitor not found', Response::HTTP_NOT_FOUND);
        }

        return $this->serializeResponse($user);
    }
}
