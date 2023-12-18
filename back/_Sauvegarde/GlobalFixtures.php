<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Availability;
use App\Entity\Children;
use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Comment;
use App\Entity\Order;
use App\Entity\Subscription;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GlobalFixtures extends Fixture
{
    private $connexion;
    private $passwordHasher;

    public function __construct(Connection $connexion, UserPasswordHasherInterface $passwordHasher)
    {
        $this->connexion = $connexion;
        $this->passwordHasher = $passwordHasher;
    }

    private function truncate()
    {
        // Unactive foreign key check to make truncate command working
        // TRUNCATE set Auto Increment and Id start at 1
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE `user`');
        $this->connexion->executeQuery('TRUNCATE TABLE `availability`');
        $this->connexion->executeQuery('TRUNCATE TABLE `chat`');
        $this->connexion->executeQuery('TRUNCATE TABLE `chat_message`');
        $this->connexion->executeQuery('TRUNCATE TABLE `children`');
        $this->connexion->executeQuery('TRUNCATE TABLE `comment`');
        $this->connexion->executeQuery('TRUNCATE TABLE `order`');
        $this->connexion->executeQuery('TRUNCATE TABLE `subscription`');
    }

    public function load(ObjectManager $manager): void
    {
        $this->truncate();

        $faker = Faker::create('fr_FR');

        $users = [];

        //* create 1 admin user
        $admin = new User();
        $admin->setEmail('admin@obaby.fr');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Obaby');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setParent(1);
        $admin->setSubscriber(1);
        $admin->setStatus(1);

        //? persist admin object
        $manager->persist($admin);

        //* create 100 fake users
        for ($i = 0; $i < 100; $i++) {
            
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setRoles(['ROLE_USER']);
            $user->setJob($faker->jobTitle());
            $user->setExperienceDuration($faker->numberBetween(1, 10));
            $user->setParent($faker->numberBetween(0, 1));
            $user->setSubscriber($faker->numberBetween(0, 1));
            $user->setIsProfessional($faker->numberBetween(0, 1));
            $user->setStatus($faker->numberBetween(0, 1));
            $user->setStreet($faker->streetAddress());
            $user->setCity($faker->city());
            $user->setZip($faker->postcode());
            $user->setLongitude($faker->longitude());
            $user->setLatitude($faker->latitude());
            $user->setDescription($faker->text());
            $user->setProfilPicture($faker->imageUrl(640, 480, 'people', true));
            $user->setHourPrice($faker->numberBetween(10, 100));
            $user->setVehicle($faker->numberBetween(0, 1));
            $user->setMaxArea($faker->numberBetween(10, 100));
            $user->setBirthday($faker->dateTimeBetween('-30 years', '-18 years'));
            $user->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
            $user->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));

            $users[] = $user;

            //? persist user object
            $manager->persist($user);
        }

        $availabilities = [];

        //* create 5 availabily 
        foreach($users as $user){

            for($i = 0; $i < 5; $i++){

                $availability = new Availability();
                $availability->setUser($user);
                $availability->setIsRecurrent($faker->numberBetween(0, 1));
                $availability->setStartDate($faker->dateTimeBetween('-6 months', 'now'));
                $availability->setEndDate($faker->dateTimeBetween('-6 months', 'now'));
                $availability->setStatus($faker->numberBetween(0, 1));
                $availability->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 months', 'now')));
                $availability->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));

                $availabilities[] = $availability;

                //? persist availability object
                $manager->persist($availability);
            }
        }
        
        $childrens = [];

        //* create between 1 and 3 Children for each user parent
        foreach ($users as $user) {

            if ($user->isParent() == 1) {
                for ($i = 0; $i < $faker->numberBetween(1, 3); $i++) {
                    
                    $children = new Children();

                    $children->setUser($user);
                    $children->setGender('m');
                    $children->setFirstName($faker->firstName());
                    $children->setBirthday($faker->dateTimeBetween('-6 years', '-1 years'));
                    $children->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 months', 'now')));
                    $children->setUpdatedAt($faker->dateTimeBetween('-1 months', 'now'));

                    $childrens[] = $children;

                    //? persist children object
                    $manager->persist($children);
                }
            }
        }

        $chats = [];
        $chatsMessages = [];

        //* create 1 to 5 chat for each couple of users
        foreach ($users as $user1) {

            for ($j = 0; $j < $faker->numberBetween(1, 5); $j++) {
                $user2 = $faker->randomElement($users);
                
                // Get a new random user if the user is the same as the first one
                while ($user2 === $user1) {
                    $user2 = $faker->randomElement($users);
                }
                  
                $chat = new Chat();
                $chat->setStatus(1);
                $chat->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
                $chat->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));
        
                $chat->addUser($user1);
                $chat->addUser($user2);
        
                $chats[] = $chat;
                $manager->persist($chat);
            }
        }

        //* For each chat create between 1 and 5 messages
        foreach ($chats as $chat) {
            // Get the participants of the chat to assign them as author of the messages
            $participants = $chat->getUser()->toArray();
            
            $author1 = $participants[0];
            $author2 = $participants[1];
            // Create between 1 and 5 messages for each chat
            for ($i = 0; $i < $faker->numberBetween(1, 5); $i++) {

                $chatMessage = new ChatMessage();
                $chatMessage->setChat($chat);
                $chatMessage->setContent($faker->text());
                $chatMessage->setStatus(1);
                $chatMessage->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
                $chatMessage->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));

                // Get a random author between the two participants of the chat and assign it to the message
                $chatMessage->setAuthor($faker->randomElement([$author1, $author2]));

                $chatsMessages[] = $chatMessage;
                $manager->persist($chatMessage);
            }
        }

        //* create between 1 and 5 Comment for each user between 2 
        foreach($users as $user){

            if($user->isParent() == 1){
                for($i = 0; $i < $faker->numberBetween(1, 5); $i++){

                    $comment = new Comment();
                    $comment->setAuthor($user);
                    $comment->setSubject($faker->randomElement($users));
                    $comment->setContent($faker->text());
                    $comment->setRate(4);
                    $comment->setStatus(1);
                    $comment->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
                    $comment->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));

                    //? persist comment object
                    $manager->persist($comment);
                }
            }
        }

        //* create 1 unique Subscribtion for 6 month period
        $subscription = new Subscription();
        $subscription->setDurationMonth(6);
        $subscription->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
        $subscription->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));

        //? persist subscription object
        $manager->persist($subscription);

        //* create one Order contain the Subscription for each user where subscriber = 1 
        foreach($users as $user){

            if($user->isSubscriber() == 1){

                $order = new Order();
                $order->setUser($user);
                $order->setSubscription($subscription);
                $order->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
                $order->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));

                //? persist order object
                $manager->persist($order);
            }
        }

        //? flush all the persisted objects  
        $manager->flush();
    }
}