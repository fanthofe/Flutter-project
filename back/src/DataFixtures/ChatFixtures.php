<?php

namespace App\DataFixtures;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use Faker\Factory as Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChatFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        for ($i = 1; $i < 99; $i++) {
            for ($j = 1; $j < random_int(1,4); $j++) {
                $chat = new Chat(); 
                $otherUser = $i;
                while($i == $otherUser){
                    $otherUser = random_int(1,99);
                }
                $chat->addUser($this->getReference('user-'.$i));
                $chat->addUser($this->getReference('user-'.$otherUser));
                
                $chat->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
                $chat->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));     
                
                for ($j = 1; $j < random_int(1,4); $j++) {
                    $chatMessage = new ChatMessage(); 
                    $chatMessage->setContent($faker->text());
                    $chatMessage->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
                    $chatMessage->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));     
                    $chatMessage->setChat($chat);
                    $chatMessage->setAuthor($this->getReference('user-'.$i));
                    $manager->persist($chatMessage);
                }
                for ($j = 1; $j < random_int(1,4); $j++) {
                    $chatMessage = new ChatMessage(); 
                    $chatMessage->setContent($faker->text());
                    // $chatMessage->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
                    // $chatMessage->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));     
                    $chatMessage->setChat($chat);
                    $chatMessage->setAuthor($this->getReference('user-'.$otherUser));
                    $manager->persist($chatMessage);
                }
                $manager->persist($chat);        
            }
        }


        $manager->flush();
    }
    public function getDependencies()
    {
       return [
          UserFixtures::class  
       ];
    }
}