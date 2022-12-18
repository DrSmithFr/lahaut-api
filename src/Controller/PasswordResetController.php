<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\SerializerAware;
use App\Model\ResetPasswordModel;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Password reset")
 */
class PasswordResetController extends AbstractController
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
     * Request a password reset token (by mail).
     * @OA\RequestBody(
     *     @OA\Schema(
     *        type="object",
     *        example={"username": "user@gmail.com"}
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="User connected",
     *     @OA\Schema(
     *        type="object",
     *        example={"info": "mail sent"}
     *     )
     * )
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserService $userService
     * @param EntityManagerInterface $entityManager
     * @return Response | JsonResponse
     * @throws NonUniqueResultException
     */
    #[Route(path: '/password_reset', name: 'request', methods: ['post'])]
    public function passwordResetRequestAction(
        Request                $request,
        UserRepository         $userRepository,
        UserService            $userService,
        EntityManagerInterface $entityManager,
    )
    {
        $username = $request->get('username');
        $user = $userRepository->findOneByEmail($username);

        if (null === $user) {
            return new JsonResponse(
                [
                    'error' => 'User not recognised',
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        $userService->generateResetToken($user);

        $entityManager->flush();

        return new JsonResponse(
            [
                'info' => 'mail send',
            ],
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Reset password with token.
     * @OA\RequestBody(@Model(type=ResetPasswordModel::class))
     * @OA\Response(
     *     response=200,
     *     description="Update User password",
     *     @OA\Schema(
     *        type="object",
     *        example={"info": "Password changed"}
     *     )
     * )
     * @param $resetPasswordModel
     * @param UserRepository $userRepository
     * @param UserService $userService
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route(path: '/password_reset', name: 'app_password_reset', methods: ['patch'])]
    public function passwordResetAction(
        #[MapEntity(class: ResetPasswordModel::class)] $resetPasswordModel,
        UserRepository                                 $userRepository,
        UserService                                    $userService,
        EntityManagerInterface                         $entityManager
    ): JsonResponse
    {
        $user = $userRepository->getUserByPasswordResetToken($resetPasswordModel->getToken());

        if (null === $user) {
            return new JsonResponse(
                [
                    'error' => 'token not valid.',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->setPlainPassword($resetPasswordModel->getPassword());
        $user->setPasswordRequestedAt(null);
        $user->setConfirmationToken(null);

        $userService->updatePassword($user);
        $entityManager->flush();

        return new JsonResponse(
            [
                'info' => 'Password changed',
            ],
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Check if reset password token is valid.
     * @OA\RequestBody(
     *     @OA\Schema(
     *        type="object",
     *        example={"token": "xxxxxxxxxxxxxxx"}
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Update User password",
     *     @OA\Schema(
     *        type="object",
     *        example={"info": "Password changed"}
     *     )
     * )
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route(path: '/password_reset', name: 'app_password_reset_token_validity', methods: ['get'])]
    public function isPasswordResetTokenValidAction(
        Request        $request,
        UserRepository $userRepository
    ): JsonResponse
    {
        $token = $request->get('token');
        $user = $userRepository->getUserByPasswordResetToken($token);

        if (null === $user) {
            return new JsonResponse(
                [
                    'error' => 'token not valid.',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            [
                'info' => 'token valid.',
            ],
            Response::HTTP_OK
        );
    }
}
