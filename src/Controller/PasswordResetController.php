<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Model\ResetPasswordModel;
use App\Repository\UserRepository;
use App\Service\MailerService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Password reset")
 */
#[Security(name: null)]
class PasswordResetController extends AbstractApiController
{
    /**
     * Request a password reset token (by mail).
     * @OA\RequestBody(
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      @OA\Schema(
     *         type="object",
     *         example={"username": "user@gmail.com"}
     *      )
     *    )
     * )
     * @OA\Response(response=202, description="User connected")
     * @OA\Response(response=403, description="User not recognised")
     *
     * @param Request                $request
     * @param UserRepository         $userRepository
     * @param UserService            $userService
     * @param EntityManagerInterface $entityManager
     * @param MailerService          $mailerService
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[Route(path: '/password_reset', name: 'request', methods: ['post'])]
    public function passwordResetRequestAction(
        Request                $request,
        UserRepository         $userRepository,
        UserService            $userService,
        EntityManagerInterface $entityManager,
        MailerService          $mailerService
    ): JsonResponse
    {
        $username = $request->get('username');
        $user = $userRepository->findOneByEmail($username);

        if (null === $user) {
            return $this->messageResponse('User not recognised', Response::HTTP_FORBIDDEN);
        }

        $userService->generateResetToken($user);
        $mailerService->sendResetPasswordMail($user);

        $entityManager->flush();

        return $this->messageResponse('mail sent', Response::HTTP_ACCEPTED);
    }

    /**
     * Reset password with token.
     * @OA\RequestBody(@Model(type=ResetPasswordModel::class))
     * @OA\Response(response=202, description="Update User password")
     * @OA\Response(response=400, description="Token not valid")
     *
     * @param                        $resetPasswordModel
     * @param UserRepository         $userRepository
     * @param UserService            $userService
     * @param EntityManagerInterface $entityManager
     *
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
            return $this->messageResponse('token not valid', Response::HTTP_BAD_REQUEST);
        }

        $user->setPlainPassword($resetPasswordModel->getPassword());
        $user->setPasswordRequestedAt(null);
        $user->setConfirmationToken(null);

        $userService->updatePassword($user);
        $entityManager->flush();

        return $this->messageResponse('Password changed', Response::HTTP_ACCEPTED);

    }

    /**
     * Check if reset password token is valid.
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(type="object", example={"token": "xxxxxxxxxxxxxxx"})
     *   )
     * )
     * @OA\Response(response=202, description="Update User password")
     * @OA\Response(response=400, description="Token not valid")
     *
     * @param Request        $request
     * @param UserRepository $userRepository
     *
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
            return $this->messageResponse('token not valid.', Response::HTTP_BAD_REQUEST);
        }

        return $this->messageResponse('token valid.', Response::HTTP_ACCEPTED);
    }
}
