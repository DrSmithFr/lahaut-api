<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Activity\Slot;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Repository\Activity\SlotRepository;
use App\Service\DateService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * Retrieve all activity slots of a monitor for the given day
     * @OA\Response(
     *     response=200,
     *     description="Returns all activity slots of a monitor for the given day",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class, groups={"Default", "booking"}))
     *     )
     * )
     * @param Request        $request
     * @param SlotRepository $slotRepository
     * @return JsonResponse
     */
    #[Route(
        path: '/monitor/slots/{date}',
        name: 'app_monitor_slots_list',
        requirements: [
            'date' => '\d{4}-\d{2}-\d{2}',
        ],
        methods: ['GET']
    )]
    public function listMonitorSlots(
        Request $request,
        SlotRepository $slotRepository,
        DateService $dateService
    ): JsonResponse {
        $date = $dateService->createFromDateString($request->get('date'));

        if (!$date) {
            return $this->messageResponse('Invalid start date', Response::HTTP_BAD_REQUEST);
        }

        $monitor = $this->getUser();

        $slots = $slotRepository
            ->findAllBetween(
                $date,
                $date->modify('+1 day'),
                $monitor
            );

        return $this->serializeResponse($slots, ['Default', 'booking']);
    }
}
