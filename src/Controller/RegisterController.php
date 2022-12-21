<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\UserEnum;
use App\Form\RegisterType;
use App\Model\FormErrorModel;
use App\Model\RegisterModel;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
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
     * Create a new customer account
     * @OA\RequestBody(@Model(type=RegisterModel::class))
     * @OA\Response(response=201, description="User created")
     * @OA\Response(
     *     response="400",
     *     description="Bad request",
     *     @Model(type=FormErrorModel::class)
     * )
     */
    #[Route(path: '/register/customer', name: 'app_register_user', methods: ['post'])]
    final public function registerUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserService $userService
    ): JsonResponse {
        return $this->registerAnUserWithRoles(
            [UserEnum::CUSTOMER->getRole()],
            $request,
            $entityManager,
            $userRepository,
            $userService,
        );
    }

    /**
     * Create a new monitor account
     * @OA\RequestBody(@Model(type=RegisterModel::class))
     * @OA\Response(response=201, description="User created")
     * @OA\Response(
     *     response="400",
     *     description="Bad request",
     *     @Model(type=FormErrorModel::class)
     * )
     */
    #[Route(path: '/register/monitor', name: 'app_register_monitor', methods: ['post'])]
    final public function registerMonitor(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserService $userService
    ): JsonResponse {
        return $this->registerAnUserWithRoles(
            [UserEnum::MONITOR->getRole()],
            $request,
            $entityManager,
            $userRepository,
            $userService,
        );
    }

    private function registerAnUserWithRoles(
        array $roles,
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

        foreach ($roles as $role) {
            if (!UserEnum::tryFrom($role)) {
                throw new InvalidArgumentException('Invalid role');
            }
        }

        $user->setRoles($roles);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }
}
