<?php

declare(strict_types=1);

namespace App\Controller\Authentication;

use App\Controller\AbstractApiController;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Form\Register\RegisterCustomerType;
use App\Form\Register\RegisterMonitorType;
use App\Model\Form\FormErrorModel;
use App\Model\Register\RegisterCustomerModel;
use App\Model\Register\RegisterMonitorModel;
use App\Repository\UserRepository;
use App\Service\User\UserService;
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
     * Create a new customer account
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="username", type="string", example="john.doe@mail.fr")
     *     )
     * )
     * @OA\Response(response=200, description="Email can be used")
     * @OA\Response(response="406", description="Email already used")
     */
    #[Route(path: '/auth/register/available', name: 'app_register_available', methods: ['post'])]
    final public function registerAvailable(
        Request $request,
        UserRepository $userRepository,
    ): JsonResponse {
        $email = $request->request->get('username');
        $user = $userRepository->findOneByEmail($email);

        if ($user instanceof User) {
            return $this->messageResponse('Email already used', Response::HTTP_NOT_ACCEPTABLE);
        }

        return $this->messageResponse('Email can be used');
    }

    /**
     * Create a new customer account
     * @OA\RequestBody(@Model(type=RegisterCustomerModel::class))
     * @OA\Response(response=201, description="User created")
     * @OA\Response(
     *     response="400",
     *     description="Bad request",
     *     @Model(type=FormErrorModel::class)
     * )
     */
    #[Route(path: '/auth/register/customer', name: 'app_register_user', methods: ['post'])]
    final public function registerUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserService $userService
    ): JsonResponse {
        $data = new RegisterCustomerModel();

        $form = $this->handleJsonFormRequest(
            $request,
            RegisterCustomerType::class,
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

        $user->setRoles([RoleEnum::CUSTOMER]);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }

    /**
     * Create a new monitor account
     * @OA\RequestBody(@Model(type=RegisterMonitorModel::class))
     * @OA\Response(response=201, description="User created")
     * @OA\Response(
     *     response="400",
     *     description="Bad request",
     *     @Model(type=FormErrorModel::class)
     * )
     */
    #[Route(path: '/auth/register/monitor', name: 'app_register_monitor', methods: ['post'])]
    final public function registerMonitor(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserService $userService
    ): JsonResponse {
        $data = new RegisterMonitorModel();

        $form = $this->handleJsonFormRequest(
            $request,
            RegisterMonitorType::class,
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

        $user->setRoles([RoleEnum::MONITOR]);

        $user->setIdentity(
            (new User\Identity())
                ->setFirstName($data->getFirstName())
                ->setLastName($data->getLastName())
                ->setPhone($data->getPhone())
        );

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }
}
