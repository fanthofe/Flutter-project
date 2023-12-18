<?php 

namespace App\EventSubscriber;

use App\Entity\User;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var ParameterBagInterface
     */
    private $parameter;

    /**
     * @var RequestStack
     */
    private $requestStack;



    public function __construct(UserPasswordHasherInterface $passwordEncoder, MailerInterface $mailer, ParameterBagInterface $parameter, RequestStack $requestStack)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->parameter = $parameter;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setData'],
            BeforeEntityUpdatedEvent::class => ['updateData']
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     * @return void
     */
    public function setData(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (($entity instanceof User)){
            $this->setPassword($entity);
        }
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function updateData(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();
        $request = $this->requestStack->getCurrentRequest();

        if (($entity instanceof User)) {
            
            $password = $request->request->get('User')['password'];
            $passwordInfo = password_get_info($password);

            if ($passwordInfo['algoName'] !== 'bcrypt') {
                $this->setPasswordAndNotifyUser($entity);
            } else {
                return;
            }
        }
    }

    /**
     * Hash password
     * 
     * @param User $entity
     */
    public function setPassword(User $entity): void
    {
        $password = $entity->getPassword();
        $entity->setPassword($this->passwordEncoder->hashPassword($entity, $password));
    }

    /**
     * Hash password and notify user send email with new plaintext password
     * 
     * @param User $entity
     */
    public function setPasswordAndNotifyUser(User $entity): void
    {
        $password = $entity->getPassword();
        $this->notifyUser($entity);
        
        $entity->setPassword($this->passwordEncoder->hashPassword($entity, $password));
    }

    public function notifyUser(User $entity): void
    {
        $email = (new Email())
            ->from($this->parameter->get('admin_email'))
            ->to($entity->getEmail())
            ->subject('Votre nouveau mot de passe')
            ->text('Votre nouveau mot de passe est : ' . $entity->getPassword())
        ;

        $this->mailer->send($email);
    }
}