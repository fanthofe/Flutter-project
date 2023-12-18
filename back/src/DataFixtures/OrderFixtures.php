<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Faker\Factory as Faker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        
        for($i = 1; $i < 99; $i++){
            $order = new Order();
            $order->setPaymentType($faker->randomElement(['CB', 'Paypal', 'Chèque', 'Espèce']));
            // $order->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
            // $order->setUpdatedAt($faker->dateTimeBetween('-6 months', 'now'));
            $order->setUser($this->getReference('user-'.$i));
            $order->setSubscription($this->getReference('subscription-1'));
            $manager->persist($order);
        }        

        $manager->flush();
    }
    public function getDependencies()
    {
       return [
            UserFixtures::class,
            SubscriptionFixtures::class
       ];
    }
}