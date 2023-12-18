<?php

namespace App\EventSubscriber;

use App\Entity\ChatMessage;
use App\Entity\Chat;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\PublisherInterface;

class ChatMessageEventSubscriber implements EventSubscriber
{
    private $publisher;
    private $container;
    private $serializer;

    public function __construct(
        PublisherInterface $publisher, 
        ContainerInterface $container, 
        SerializerInterface $serializer
        )
    {
        $this->publisher = $publisher;
        $this->container = $container;
        $this->serializer = $serializer;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist, 
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof ChatMessage) {
            $this->publishMessageUpdate($entity);
        }
    }

    private function publishMessageUpdate(ChatMessage $chatMessage)
    {   
        $chatId = $chatMessage->getChat()->getId();

        // update the chat updatedAt on each new message to sort the chat list by last message
        $chatMessage->getChat()->setUpdatedAt(new \DateTime());
        $this->container->get('doctrine')->getManager()->flush();

        $mercureHubUrl = $this->container->getParameter('mercure_hub_url');
        $messageContent = $this->serializer->serialize($chatMessage, 'json', ['groups' => 'chat_message:read']);

        $update = new Update(
            'chats/' . $chatId, 
            $messageContent 
        );

        $this->publisher->__invoke($update, $mercureHubUrl);
    }
}