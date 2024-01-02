<?php

namespace App\EventSubscriber;

use App\Entity\ChatMessage;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\PublisherInterface;

class DoctrineChatMessageSubscriber implements EventSubscriber
{   

    public function __construct(
        PublisherInterface $publisher, 
        ContainerInterface $container, 
        SerializerInterface $serializer,
        HubInterface $hub
        )
    {
        $this->publisher = $publisher;
        $this->container = $container;
        $this->serializer = $serializer;
        $this->hub = $hub;
    }

    /**
     * Get the subscribed events
     * @return array
     */
    public function getSubscribedEvents() : array
    {
        return [
            Events::postPersist, 
        ];
    }

    /**
     * Get Entity on post persist event
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof ChatMessage) {
            $this->publishMessageUpdate($entity);
        }
    }

    /**
     * Publish a message update to mercure hub
     * @param ChatMessage $chatMessage
     * @return void
     */
    private function publishMessageUpdate(ChatMessage $chatMessage)
    {   

        $mercureHubUrl = $this->container->getParameter('mercure_hub_url');
        $messageContent = $this->serializer->serialize($chatMessage, 'json', ['groups' => 'chat_message:read']);

        $update = new Update(
            'chats_messages', 
            $messageContent 
        );

       $this->hub->publish($update); 

    }
}