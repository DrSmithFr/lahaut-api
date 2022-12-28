<?php

namespace App\Controller;

use App\Entity\Chat\Conversation;
use App\Entity\Chat\Message;
use App\Form\Chat\NewConversationType;
use App\Model\Chat\NewConversationModel;
use App\Repository\Chat\ConversationRepository;
use App\Repository\Chat\MessageRepository;
use App\Service\ChatService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Chat")
 */
class ChatController extends AbstractApiController
{
    /**
     * Initialise a new conversation between two users
     * @OA\RequestBody(@Model(type=NewConversationModel::class))
     * @OA\Response(
     *     response=200,
     *     description="User connected",
     *     @Model(type=Conversation::class)
     * )
     * @OA\Response(response="401", description="Cannot connect user")
     */
    #[Route(path: '/conversations', name: 'app_conversation_new', methods: ['post'])]
    public function createConversation(
        Request $request,
        EntityManagerInterface $entityManager,
        ConversationRepository $conversationRepository,
        ChatService $chatService
    ): JsonResponse {
        $data = new NewConversationModel();

        $form = $this->handleJsonFormRequest(
            $request,
            NewConversationType::class,
            $data
        );

        if (!$form->isValid()) {
            return $this->formErrorResponse($form, Response::HTTP_BAD_REQUEST);
        }

        $otherUsers = $data->getUsers();

        if (!count($otherUsers)) {
            return $this->messageResponse('No other users provided', Response::HTTP_BAD_REQUEST);
        }

        $currentUser = $this->getUser();

        if (in_array($currentUser, $otherUsers, true)) {
            return $this->messageResponse(
                'Cannot create a conversation with yourself',
                Response::HTTP_FORBIDDEN
            );
        }

        $users = [$currentUser, ...$otherUsers];

        // Check if conversation already exists
        $conversation = $conversationRepository->getOneByParticipants($users);

        if ($conversation === null) {
            $conversation = $chatService->createNewConversationBetween($users);
        }

        $entityManager->persist($conversation);
        $entityManager->flush();

        return $this->serializeResponse($conversation, ['Default'], Response::HTTP_CREATED);
    }


    /**
     * Retrieve all conversations for the current user
     * @OA\Response(
     *     response=200,
     *     description="User connected",
     *     @Model(type=Conversation::class)
     * )
     */
    #[Route(path: '/conversations', name: 'app_conversation_list', methods: ['get'])]
    public function listConversation(
        ConversationRepository $conversationRepository
    ): JsonResponse {
        $conversations = $conversationRepository
            ->findAllByUser($this->getUser());

        return $this->serializeResponse($conversations);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the messages of a conversation",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Message::class))
     *     )
     * )
     * @param Conversation      $conversation
     * @param MessageRepository $messageRepository
     *
     * @return JsonResponse
     */
    #[Route(
        path: '/conversations/{uuid}',
        name: 'app_conversation_messages',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getConversationMessages(
        #[MapEntity(class: Conversation::class)] Conversation $conversation,
        MessageRepository $messageRepository
    ): JsonResponse {
//        $this->denyAccessUnlessGranted('view', $conversation);

        $messages = $messageRepository
            ->findAllByConversation($conversation);

        return $this->serializeResponse($messages);
    }


    /**
     * Get a conversation by id
     *
     * @param Conversation           $conversation
     * @param ChatService            $chatService
     * @param EntityManagerInterface $entityManager
     * @param Request                $request
     *
     * @return JsonResponse
     */
    #[Route(
        path: '/conversations/{uuid}',
        name: 'app_conversation_post_message',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function postConversationMessages(
        #[MapEntity(class: Conversation::class)] Conversation $conversation,
        ChatService $chatService,
        EntityManagerInterface $entityManager,
        Request $request
    ): JsonResponse {
        $user = $this->getUser();
        $content = $request->get('content', '');

        $message = $chatService->addMessageToConversation($conversation, $user, $content);

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->messageResponse('Message sent', Response::HTTP_CREATED);
    }
}
