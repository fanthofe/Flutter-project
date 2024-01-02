<?php

namespace App\Service;

use App\Message\ContacMessage;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContactService
 * Get data from the request, sanitize and validate it
 * and send it to the messenger bus
 */
class ContactService
{   
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var MessageBusInterface
     */
    protected $bus;

    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(RequestStack $requestStack, MessageBusInterface $bus, PublisherInterface $publisher, ContainerInterface $container, HubInterface $hub)
    {
        $this->requestStack = $requestStack;
        $this->bus = $bus;
        $this->publisher = $publisher;
        $this->container = $container;
        $this->hub = $hub;
    }

    /**
     * Get contact data send from the current request
     * and return an array with data
     * @return array
     */
    private function getRequestData() : array
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = $request->getContent();
        $data = json_decode($data, true);
        return $data;
    }

    /**
     * Sanitize data from the request
     * and return an array with sanitized data
     * @return array
     */
    private function sanitizeData()
    {    
        $data = $this->getRequestData();

        $senderFirstName = htmlspecialchars(trim($data['firstName']), ENT_QUOTES, 'UTF-8');
        $senderLastName = htmlspecialchars(trim($data['lastName']), ENT_QUOTES, 'UTF-8');
        $senderMail = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        $subject = htmlspecialchars(trim($data['subject']), ENT_COMPAT, 'UTF-8');
        $messageContent = htmlspecialchars(trim($data['message']), ENT_QUOTES, 'UTF-8');

        return [
            'senderFirstName' => $senderFirstName,
            'senderLastName' => $senderLastName,
            'senderMail' => $senderMail,
            'subject' => $subject,
            'messageContent' => $messageContent
        ];
    }

    /**
     * Validate data from the request
     * and return an error message if data is not valid
     * @return array
     */
    private function validateData()
    {
        $data = $this->sanitizeData();

        if(empty($data['senderFirstName']) || empty($data['senderLastName']) 
            || empty($data['senderMail']) || empty($data['subject']) 
            || empty($data['messageContent'])) {
            return [
                'message' => 'Veuillez remplir tous les champs'
            ];
        }
        if(!filter_var($data['senderMail'], FILTER_VALIDATE_EMAIL)) {
            return [
                'message' => 'Veuillez entrer une adresse mail valide'
            ];
        }
        return $data;
    }

    /**
     * Send data to messenger bus
     * and return a message if the message is sent or not
     * @return array
     */
    public function sendToMessengerBus()
    {   
        $data = $this->validateData();
        // If data is not valid, return an error message
        if(isset($data['message'])) {
            return $data;
        }  
        // If data is valid, send it to the messenger bus
        $message = new ContacMessage(...array_values($data));

        if($this->bus->dispatch($message)) {
            return [
                'message' => 'Message envoyé avec succès'
            ];
        } else {
            return [
                'message' => 'Erreur lors de l\'envoi du message'
            ];
        }
    }

    /**
     * Send data to mercure hub
     * and return a message if the message is sent or not
     * @return array
     */
    public function sendToMercureHub()
    {   
        $data = $this->validateData();
     
        if(isset($data['message'])) {
            return $data;
        }

        $mercureHubUrl = $this->container->getParameter('mercure_hub_url');
        $messageContent = json_encode($data);

        $update = new Update(
            'contact_email', 
            $messageContent 
        );

        $this->hub->publish($update);

        return [
            'message' => 'Message envoyé sur le hub mercure avec succès'
        ];
    }
}