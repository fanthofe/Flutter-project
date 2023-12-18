<?php

namespace App\Controller\BackOffice;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ContactController extends AbstractController
{   

    /**
     * Get form contact data and send an email
     * @Route("/contact", name="api_contact_form", methods={"POST"})
     */
    public function postAction(
        Request $request,
        MailerInterface $mailer
    ): Response
    {
        // get the data from the request
        $data = $request->getContent();
        $data = json_decode($data, true);
        
        // initialize variables with the data
        $senderFirstName = $data['firstName'];
        $senderLastName = $data['lastName'];
        $senderMail = $data['email'];
        $subject = $data['subject'];
        $messageContent = $data['message'];

        // sanitize the data
        $senderFirstName = htmlspecialchars(trim($senderFirstName), ENT_QUOTES, 'UTF-8');
        $senderLastName = htmlspecialchars(trim($senderLastName), ENT_QUOTES, 'UTF-8');
        $senderMail = filter_var(trim($senderMail), FILTER_SANITIZE_EMAIL);
        $subject = htmlspecialchars(trim($subject), ENT_COMPAT, 'UTF-8');
        $messageContent = htmlspecialchars(trim($messageContent), ENT_QUOTES, 'UTF-8');

        // send the email
        $email = (new Email())
            ->from($senderMail)
            ->to('obaby.project@gmail.com')
            ->subject('Nouveau message de l\'application Obaby : ' . $subject)
            ->html('<p>Message reçu de la part de '.$senderFirstName.' '.$senderLastName.' : </p><br><p>'.$messageContent.'</p');
        
        // try to send the email and catch an error if it fails
        try {
            $mailer->send($email);
            // return a success message
            return new JsonResponse('Message envoyé');
        } catch (\Exception $e) {
            // else return an error message with a 500 error code
            return new JsonResponse('Erreur lors de l\'envoi du message : '.$e->getMessage(), 500);
        }
    }
    

}