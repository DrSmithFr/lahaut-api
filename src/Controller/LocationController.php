<?php

namespace App\Controller;

use App\Entity\Fly\FlyLocation;
use App\Repository\Fly\FlyLocationRepository;
use App\Repository\Fly\FlyTypeRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Fly - Locations")
 */
class LocationController extends AbstractApiController
{
    /**
     * Retrieve all fly location
     * @OA\Response(
     *     response=200,
     *     description="Returns all fly location",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=FlyLocation::class, groups={"Default", "monitor"}))
     *     )
     * )
     */
    #[Route(
        path: '/public/locations',
        name: 'app_locations_list',
        methods: ['get']
    )]
    public function listAllLocations(
        FlyLocationRepository $flyLocationRepository,
    ): JsonResponse {
        $locations = $flyLocationRepository->findAll();
        return $this->serializeResponse($locations);
    }

    /**
     * Retrieve all fly location
     * @OA\Response(
     *     response=200,
     *     description="Returns all fly location",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=FlyLocation::class, groups={"Default", "monitor"}))
     *     )
     * )
     */
    #[Route(
        path: '/public/locations/{identifier}/types',
        name: 'app_location_types_list',
        methods: ['get']
    )]
    public function listAllFlyTypes(
        #[MapEntity(class: FlyLocation::class, mapping: ['identifier' => 'identifier'])] FlyLocation $location,
        FlyTypeRepository $flyTypeRepository,
    ): JsonResponse {
        $types = $flyTypeRepository->findAllByFlyLocation($location);
        return $this->serializeResponse($types);
    }
}
