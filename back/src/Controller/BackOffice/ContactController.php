<?php

namespace App\Controller\BackOffice;

use App\Message\ContacMessage;
use App\Service\ContactService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{   

    /**
     * Send the contact form to the messenger bus
     * @Route("/contact", name="api_contact_form", methods={"POST"})
     */
    public function sendAsyncContactMail(
        ContactService $contactService
    ): Response
    {   
        $messangerResponse = $contactService->sendToMessengerBus();
        $mercureHubresponse = $contactService->sendToMercureHub();

        return new JsonResponse(
            [
                'messenger' => $messangerResponse,
                'mercure' => $mercureHubresponse
            ],
            Response::HTTP_OK,
            ['location' => '/contact']
        );
    }
    

}