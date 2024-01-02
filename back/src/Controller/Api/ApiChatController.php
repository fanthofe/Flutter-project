<?php

namespace App\Controller\Api;

use App\Message\ChatMessage;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiChatController extends AbstractController
{
     /** 
     * Get all chats for one user 
     * Each chat contains the last participant response message
     * 
     * @Route("/api/chats/user/{userId}", name="api_user_chats")
     */
    public function getUserChats($userId, UserRepository $userRepository ,ChatRepository $chatRepository)
    {
        $user = $userRepository->find($userId);
        $chats = $chatRepository->findChatsForOneUser($user);
        // return a json response with the chats
        return $this->json($chats, 200, []);
    }

    /**
     * Check if chat exists between two users
     * @Route("/api/chats/user/{userId}/user/{otherUserId}", name="api_check_chat")
     */
    public function checkChat($userId, $otherUserId, UserRepository $userRepository, ChatRepository $chatRepository)
    {
        $user = $userRepository->find($userId);
        $otherUser = $userRepository->find($otherUserId);
        $chat = $chatRepository->findChatBetweenTwoUsers($user, $otherUser);
        // get chat id if exists
        $chat = $chat ? $chat->getId() : null;
        // return a json response with the chat
        return $this->json($chat, 201, []);
    }

    /**
     * Create a new chat message for a chat and dispatch it to the message bus 
     * and to mercure using doctrine event subscriber
     * 
     * @Route("/api/async_chat_messages", name="api_post_async_messages", methods={"POST"})
     */
    public function newMessage(
        Request $request, 
        MessageBusInterface $bus
        )
    {
        $data = json_decode($request->getContent(), true);
        $chatMessage = new ChatMessage($data['author'], $data['content'], $data['chat']);
        $bus->dispatch($chatMessage);

        return $this->json('message reçu', 200, []);
    }

}