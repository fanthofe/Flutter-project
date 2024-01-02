<?php

namespace App\EventSubscriber;

use App\Entity\Chat;
use App\Repository\ChatRepository;
use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ChatSubscriber implements EventSubscriberInterface
{
    private $chatRepository;
    private $requestStack;

    public function __construct(ChatRepository $chatRepository, RequestStack $requestStack)
    {
        $this->chatRepository = $chatRepository;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            // VIEW is triggered after API platform execute the controller on /api/chats route
            // PRE_WRITE is triggered before response writing
            KernelEvents::VIEW => ['checkExistingChat', EventPriorities::PRE_WRITE],
        ];
    }

    public function checkExistingChat(ViewEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
     
        // dd(RequestAttributesExtractor::extractAttributes($request));
        // check if event is triggered by a POST request on a Chat resource
        if (
            !($attributes = RequestAttributesExtractor::extractAttributes($request))
            || !$attributes['receive']
            || !$attributes['resource_class']
            || Chat::class !== $attributes['resource_class']
        ) {
            return;
        }
        
        $requestData = json_decode($request->getContent(), true);
        
        if (!isset($requestData['user'])) {
            return;
        }

        // Get users id from IRI in request data /api/users/{id}
        $userUrls = $requestData['user'];
        $userIds = array_map(function ($url) {
            return intval(substr($url, strrpos($url, '/') + 1));
        }, $userUrls);

        // check if chat exists between these two users
        $existingChat = $this->chatRepository->findChatBetweenTwoUsers($userIds[0], $userIds[1]);

        if ($existingChat) {
            // cancel new chat creation by returning existing chat from findChatBetweenTwoUsers method 
            $event->setControllerResult($existingChat);
        }
    }
}
