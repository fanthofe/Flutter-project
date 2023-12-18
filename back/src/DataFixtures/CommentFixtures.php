<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Faker\Factory as Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        for ($i = 1; $i < 99; $i++) {
            for ($j = 0; $j < random_int(0,3); $j++) {
                $comment = new Comment(); 
                $comment->setContent($faker->text());
                $comment->setRate(random_int(1,5));
                $comment->setStatus(true);

                if($i >= 50){
                    $num = $i - 49;
                    $comment->setSubject($this->getReference('user-'.$num)); 
                }else{
                    $num = $i + 49;
                    $comment->setSubject($this->getReference('user-'.$num)); 
                }
                $comment->setAuthor($this->getReference('user-'.$i)); 
                // $comment->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
                // $comment->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));     
                $manager->persist($comment);        
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