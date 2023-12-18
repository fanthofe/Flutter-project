<?php

namespace App\Controller\Api;

use ApiPlatform\Core\OpenApi\Model\Parameter;
use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Param;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiAuthController extends AbstractController
{   
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * Register a new user
     * @Route("/api/register", name="api_user_register", methods={"POST"})
     */
    public function apiUserRegister(
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        ParameterBagInterface $params
    ): Response
    {   
        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');
        $errors = $validator->validate($user);

        $user->setPassword(
            $hasher->hashPassword($user, $user->getPassword())
        );

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $doctrine->persist($user);
        $doctrine->flush();

        $adminMail = $params->get('admin_email');

        // send a confirmation email
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($adminMail, 'OBaby'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [],
            ['groups' => ['user:read']]
        );
    }

}