<?php

namespace App\Controller\Activity;

use App\Controller\AbstractApiController;
use App\Entity\Activity\ActivityLocation;
use App\Repository\Activity\ActivityLocationRepository;
use App\Repository\Activity\ActivityTypeRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Activity")
 */
class LocationController extends AbstractApiController
{
    /**
     * Retrieve all activity location
     * @OA\Response(
     *     response=200,
     *     description="Returns all activity location",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=ActivityLocation::class, groups={"Default", "monitor"}))
     *     )
     * )
     */
    #[Route(
        path: '/public/activities/locations',
        name: 'app_locations_list',
        methods: ['get']
    )]
    public function listAllLocations(
        ActivityLocationRepository $activityLocationRepository,
    ): JsonResponse {
        $locations = $activityLocationRepository->findAll();
        return $this->serializeResponse($locations);
    }

    /**
     * Retrieve all activity location
     * @OA\Response(
     *     response=200,
     *     description="Returns all activity location",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=ActivityLocation::class, groups={"Default", "monitor"}))
     *     )
     * )
     */
    #[Route(
        path: '/public/activities/locations/{identifier}/types',
        name: 'app_location_types_list',
        methods: ['get']
    )]
    public function listAllActivityTypes(
        #[MapEntity(
            class: ActivityLocation::class,
            mapping: ['identifier' => 'identifier']
        )]
        ActivityLocation $location,
        ActivityTypeRepository $activityTypeRepository,
    ): JsonResponse {
        $types = $activityTypeRepository->findAllByActivityLocation($location);
        return $this->serializeResponse($types);
    }
}
