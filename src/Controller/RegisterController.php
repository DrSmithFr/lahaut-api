<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\SecurityRoleEnum;
use App\Form\RegisterType;
use App\Model\FormErrorModel;
use App\Model\RegisterModel;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserService $userService
    ): JsonResponse {
        $data = new RegisterModel();

        $form = $this->handleJsonFormRequest(
            $request,
            RegisterType::class,
            $data
        );

        if (!$form->isValid()) {
            return $this->formErrorResponse($form, Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => strtolower($data->getUsername())]);

        if ($user) {
            return $this->messageResponse('Email already exit', Response::HTTP_FORBIDDEN);
        }

        $user = $userService->createUser(
            $data->getUsername(),
            $data->getPassword()
        );

        $user->setRoles([SecurityRoleEnum::USER]);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }
}
