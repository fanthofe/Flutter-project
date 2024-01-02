<?php

namespace App\MessageHandler;

use DateTimeImmutable;
use App\Entity\User;
use App\Entity\Chat;
use App\Entity\ChatMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Message\ChatMessage as MessengerChatMessage;


final class ChatMessageHandler implements MessageHandlerInterface
{  
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get data from ChatMEssage message getters 
     * and create a new chat message entity
     * @param MessengerChatMessage $message
     * @return void
     */
    public function __invoke(MessengerChatMessage $message)
    {
        $user = $this->manager->getRepository(User::class)->findOneBy(['id' => $message->getAuthorId()]);
        $chat = $this->manager->getRepository(Chat::class)->findOneBy(['id' => $message->getChatId()]);
        $content = $message->getContent();
        
        $newChatMessage = $this->newChatMessage([
            'author' => $user,
            'chat' => $chat,
            'content' => $content
        ]);
        $this->manager->persist($newChatMessage);
        $this->manager->flush();
    }

    /**
     * Create a new chat message entity
     * @param array $data
     * @return ChatMessage
     */
    private function newChatMessage($data)
    {
        $chatMessage = new ChatMessage();
        $chatMessage->setAuthor($data['author'])
            ->setChat($data['chat'])
            ->setContent($data['content'])
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setStatus(true);
        
        return $chatMessage;
    }

}
