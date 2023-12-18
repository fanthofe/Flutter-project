<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class SubscriptionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');

        $subscription = new Subscription();
        $subscription->setDescription($faker->text());
        $subscription->setPrice(10);
        $subscription->setDurationMonth(1);
        $subscription->setName('Abonnement 1 mois');
        $manager->persist($subscription);

        $this->addReference('subscription-1', $subscription);

        $manager->flush();
    }
    
}