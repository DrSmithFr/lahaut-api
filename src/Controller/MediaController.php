<?php

declare(strict_types = 1);

namespace App\Controller;

use Exception;
use App\Entity\Media;
use App\Form\MediaType;
use App\Service\MediaService;
use OpenApi\Annotations as OA;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route(path: '/medias', name: 'medias_')]
#[IsGranted('ROLE_USER')]
class MediaController extends AbstractApiController
{
    /**
     * Return BinaryFileResponse according to media mineType (for download and display)
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Media uniq identifier",
     *     required=true,
     *     @OA\Schema(type="number")
     * )
     * @OA\Response(
     *     response=200,
     *     description="The requested medias"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found"
     * )
     * @OA\Tag(name="Medias")
     * @Security(name="Bearer")
     *
     */
    #[Route(path: '/{uuid}', name: 'by_id_file', requirements: ['id'=>'\d+'], methods: ['GET'])]
    #[Security(name: 'Bearer')]
    public function getByIdAction(
        #[MapEntity(class: Media::class)] Media $media,
        MediaService $mediaService
    ): StreamedResponse {
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $media->getContentType());

        // Instead of a string, the streamed response will execute
        // a callback function to retrieve data chunks.
        $response->setCallback(
            static function () use ($media, $mediaService) {
                foreach ($mediaService->decrypt($media) as $block) {
                    echo $block;
                }
            }
        );

        return $response;
    }

    /**
     * Information about size, mineType and extension
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Media uuid identifier",
     *     required=true,
     *     @OA\Schema(type="number")
     * )
     * @OA\Response(
     *     response=200,
     *     description="The requested medias",
     *     @Model(type=Media::class, groups={"id", "Default"})
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found"
     * )
     * @OA\Tag(name="Medias")
     * @Security(name="Bearer")
     *
     *
     */
    #[Route(path: '/{uuid}/metadata', name: 'by_id_metadata', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function getMetadataByIdAction(
        #[MapEntity(class: Media::class)] Media $media
    ): JsonResponse {
        return $this->serializeResponse($media, ['Default']);
    }

    /**
     * Upload and encrypt a new media (retrieve the UUID of newly created media)
     * @OA\Parameter(
     *     name="json body",
     *     in="query",
     *     description="Json representation of a Media",
     *     required=true,
     *     @OA\Schema(ref=@Model(type=Media::class, groups={"id", "Default"}))
     * )
     * @OA\Response(
     *     response=200,
     *     description="The media has been created",
     *     @OA\Schema(
     *        type="object",
     *        example={"uuid": "gjc7834ace3-8525-4814-bf0f-b7146bc9e8ab"}
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="The id submitted in body dont match the one on url"
     * )
     * @OA\Response(
     *     response=406,
     *     description="No form submitted"
     * )
     * @OA\Tag(name="Medias")
     * @Security(name="Bearer")
     *
     * @throws Exception
     *
     *
     */
    #[Route(path: '', name: 'add', methods: ['POST'])]
    public function newAction(
        Request $request,
        EntityManagerInterface $entityManager,
        MediaService $mediaService
    ): JsonResponse {
        $media = new Media();

        $form = $this
            ->createForm(MediaType::class, $media)
            ->submit($request->files->all());

        if (!$form->isSubmitted()) {
            return $this->messageResponse(
                'No form submitted',
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }

        if (!$form->isValid()) {
            return $this->formErrorResponse($form);
        }

        $mediaService->upload($media);

        $entityManager->persist($media);
        $entityManager->flush();

        return $this->json(
            [
                'uuid' => $media->getUuid(),
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }
}
