<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\SecurityRoleEnum;
use App\Model\RegisterModel;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use RuntimeException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      @OA\Schema(type="object", example={"debug": "symfony error message"})
     *     )
     * )
     */
    #[Route(path: '/register', name: 'app_register', methods: ['post'])]
    final public function register(
        #[MapEntity(class: RegisterModel::class)] $registerForm,
        ValidatorInterface                        $validator,
        EntityManagerInterface                    $entityManager,
        UserRepository                            $userRepository,
        UserPasswordHasherInterface               $passwordHasher
    ): JsonResponse
    {
        $errors = $validator->validate($registerForm);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string)$errors;

            return new JsonResponse(
                ['debug' => $errorsString],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $userRepository->findOneBy(['email' => strtolower($registerForm->getEmail())]);

        if ($user) {
            throw new RuntimeException('Email already used');
        }

        $user = new User();

        $user
            ->setEmail(strtolower($registerForm->getEmail()))
            ->setPassword($passwordHasher->hashPassword($user, $registerForm->getPassword()))
            ->setRoles([SecurityRoleEnum::USER->getRole()]);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(
            $this->serialize($user),
            Response::HTTP_CREATED
        );
    }
}
