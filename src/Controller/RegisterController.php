<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\SecurityRoleEnum;
use App\Model\RegisterModel;
use App\Model\FormErrorModel;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use RuntimeException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @OA\Tag(name="Authentification")
 */
#[Security(name: null)]
class RegisterController extends AbstractApiController
{
    /**
     * Initialise sessions with encryption API (Token valid for 30s)
     * @OA\RequestBody(@Model(type=RegisterModel::class))
     * @OA\Response(
     *     response=201,
     *     description="User created",
     *     @Model(type=User::class)
     * )
     * @OA\Response(
     *     response="400",
     *     description="Bad request",
     *     @Model(type=FormErrorModel::class)
     * )
     */
    #[Route(path: '/register', name: 'app_register', methods: ['post'])]
    final public function register(
        #[MapEntity(class: RegisterModel::class)] $registerForm,
        ValidatorInterface                        $validator,
        EntityManagerInterface                    $entityManager,
        UserRepository                            $userRepository,
        UserService                               $userService
    ): JsonResponse
    {
        $errors = $validator->validate($registerForm);

        if (count($errors) > 0) {
            return $this->formErrorResponse($registerForm, Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => strtolower($registerForm->getEmail())]);

        if ($user) {
            throw new RuntimeException('Email already used');
        }

        $user = $userService->createUser(
            $registerForm->getEmail(),
            $registerForm->getPassword()
        );

        $user->setRoles([SecurityRoleEnum::USER]);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(
            $this->serialize($user),
            Response::HTTP_CREATED
        );
    }
}
