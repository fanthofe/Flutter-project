<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\ChildrenRepository;
use App\Repository\ChatRepository;
use App\Repository\ChatMessageRepository;
use App\Repository\CommentRepository;

class StatsService {

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager, 
        UserRepository $userRepository, 
        ChildrenRepository $childrenRepository, 
        ChatRepository $chatRepository,
        ChatMessageRepository $messageRepository,
        CommentRepository $commentRepository

    )
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->childrenRepository = $childrenRepository;
        $this->chatRepository = $chatRepository;
        $this->messageRepository = $messageRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Count all users
     * @return int
     */
    public function countUsers() : int
    {
        return $this->userRepository->countAllUsers();
    }

    /**
     * Count all parents
     * @return int
     */
    public function countParents() : int
    {
        return $this->userRepository->countAllParents();
    }

    /**
     * Calculate gardians count
     */
    public function countGardians() : int
    {
        return $this->countUsers() - $this->countParents();
    }

    /**
     * Count all children
     * @return int
     */
    public function countChildren() : int
    {
        return $this->childrenRepository->countAllChildren();
    }

    /**
     * Count all female children 'f'
     * @return int
     */
    public function countFemaleChildren() : int
    {
        return $this->childrenRepository->countAllFemaleChildren();
    }

    /**
     * Count all male children 'm'
     * @return int
     */
    public function countMaleChildren() : int
    {
        return $this->childrenRepository->countAllMaleChildren();
    }

    /**
     * Calculate male and female children percentage
     * @return array
     */
    public function childrenPercentage() : array
    {
        $total = $this->countChildren();
        $female = $this->countFemaleChildren();
        $male = $this->countMaleChildren();

        $femalePercentage = round(($female / $total) * 100);
        $malePercentage = round(($male / $total) * 100);

        return [
            'total_children' => $total,
            'female_percentage' => $femalePercentage,
            'male_percentage' => $malePercentage
        ];
    }

    /**
     * Calculate gardians per child
     * @return int
     */
    public function gardiansPerChild() : int
    {
        $gardians = $this->countGardians();
        $children = $this->countChildren();
        return round($gardians / $children);
    }

    /**
     * Count all chats
     * @return int
     */
    public function countChats() : int
    {
        return $this->chatRepository->countAllChats();
    }

    /**
     * Count all messages
     * @return int
     */
    public function countMessages() : int
    {
        return $this->messageRepository->countAllMessages();
    }

    /**
     * Calculates messages per chat
     * @return int
     */
    public function messagesPerChat() : int
    {
        $messages = $this->countMessages();
        $chats = $this->countChats();

        return round($messages / $chats);
    }

    /**
     * Calculates messages per user
     * @return int
     */
    public function messagesPerUser() : int
    {
        $messages = $this->countMessages();
        $users = $this->countUsers();

        return round($messages / $users);
    }

    /**
     * Count messages per parent
     * @return int
     */
    public function messagesPerPerent() : int
    {
        return $this->messageRepository->countAllMessagesParent();
    }

    /**
     * Count messages per gardian
     * @return int
     */
    public function messagesPerGardian() : int
    {
        return $this->messageRepository->countAllMessagesGardian();
    }

    /**
     * Count all comments
     * @return int
     */
    public function countComments() : int
    {
        return $this->commentRepository->countAllComments();
    }

    /**
     * Count all comments on parents
     * @return int
     */
    public function countCommentsOnParents() : int
    {
        return $this->commentRepository->countAllCommentsOnParents();
    }

    /**
     * Count all comments on guardians
     * @return int
     */
    public function countCommentsOnGuardians() : int
    {
        return $this->commentRepository->countAllCommentsOnGuardians();
    }

    /**
     * Calculate User comments avarage
     * @return array
     */
    public function userCommentsAvarage() : array
    {   
        $avg = $this->commentRepository->countAllComments() / $this->userRepository->countAllUsers();
        return [
            'avg' => round($avg, 2),
        ];
    }

    /**
     * Get Users Cities
     */
    public function countUsersByCity() : array
    {
        return $this->userRepository->countUsersByCity();
    }

    /**
     * Count suspended users
     */
    public function countSuspendedUsers() : int
    {
        return $this->userRepository->countSuspendedUsers();
    }


}