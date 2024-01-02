<?php

namespace App\MessageHandler;

use App\Message\ContacMessage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ContacMessageHandler implements MessageHandlerInterface
{   
    protected $mailer;
    protected $container;
    protected $publisher;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send the email received from the messenger bus
     * @param ContacMessage $message
     * @return bool
     */
    public function __invoke(ContacMessage $message)
    {
        $email = $this->createEmail($message);

        try {
            if ($this->mailer->send($email)) {
                return true;
            }
        } catch (\Throwable $th) {
            return new JsonResponse('Erreur lors de l\'envoi du message');
        }
    }

    /**
     * Create the email to send
     * @param ContacMessage $message
     * @return Email
     */
    private function createEmail(ContacMessage $message): Email
    {
        return (new Email())
            ->from($message->senderMail)
            ->to('obaby.project@gmail.com')
            ->subject('Nouveau message de l\'application Obaby : ' . $message->subject)
            ->html('<p>Message reçu de la part de ' . $message->senderFirstName . ' ' . $message->senderLastName . ' : </p><br><p>' . $message->messageContent . '</p');
    }
    
}
