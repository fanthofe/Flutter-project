<?php

namespace App\DataFixtures;

use App\Entity\Children;
use Faker\Factory as Faker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChildrenFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        for ($i = 1; $i < 99; $i++) {
            for ($j = 1; $j < random_int(1,4); $j++) {
                $children = new Children(); 
                $children->setFirstname($faker->firstName());
                $children->setBirthday($faker->dateTimeBetween('-6 months', 'now'));
                $children->setGender($faker->randomElement(['m','f']) );
                $children->setUser($this->getReference('user-'.$i));
                $children->setDescription($faker->text());
                // $children->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
                // $children->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));     
                $manager->persist($children);        
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