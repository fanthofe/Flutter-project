<?php

namespace App\Controller\Api;

use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->json($chat, 200, []);
    }

}