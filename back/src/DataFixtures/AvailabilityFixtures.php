<?php

namespace App\DataFixtures;

use App\Entity\Availability;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class AvailabilityFixtures extends Fixture implements DependentFixtureInterface
{
    
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        for ($i = 1; $i < 99; $i++) {
            for ($j = 0; $j < random_int(1, 5); $j++) {
                $availability = new Availability(); 
                $availability->setName('Disponibilité '.$i);

                $startDate = $faker->dateTimeBetween('now', '+15 days');
                $startHour = rand(16, 19);
                $startDate->setTime($startHour, 0, 0);
                $endDate = clone $startDate;

                $interval = rand(4, 8);
                $endDate = clone $startDate;

                // Not change the day if the end time is after midnight or on midnight
                if ($startDate->format('H') + $interval >= 24) {
                    $endDate->setTime(23, 59, 59);
                } else {
                    $endDate->add(new \DateInterval("PT{$interval}H"));
                }

                $availability->setStartDate($startDate);
                $availability->setEndDate($endDate);
                $availability->setUser($this->getReference('user-'.$i)); 
                $manager->persist($availability);        

                
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