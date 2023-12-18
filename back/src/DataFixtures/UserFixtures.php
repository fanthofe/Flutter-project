<?php

namespace App\DataFixtures;

use App\Entity\User;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Faker\Factory as Faker;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');

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

        $apiUrl = 'https://randomuser.me/api/';
        $imageSize = 'medium';
        $cities = [
            "Paris" => "75000",
            "Marseille" => "13000",
            "Bordeaux" => "33000",
            "Toulouse" => "31000",
        ];

        //* create 100 fake users
        for ($i = 0; $i < 100; $i++) {

            $userData = json_decode(file_get_contents($apiUrl), true);
            $userPicture = $userData['results'][0]['picture'][$imageSize];

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
            // $user->setCity($faker->city());
            // $user->setZip($faker->postcode());
            $user->setCity(array_rand($cities));
            $user->setZip($cities[$user->getCity()]);
            $user->setLongitude($faker->longitude());
            $user->setLatitude($faker->latitude());
            $user->setDescription($faker->text());
            // $user->setProfilPicture($faker->imageUrl(640, 480, 'people', true));
            $user->setProfilPicture($userPicture);
            $user->setHourPrice($faker->numberBetween(10, 100));
            $user->setVehicle($faker->numberBetween(0, 1));
            $user->setMaxArea($faker->numberBetween(10, 100));
            $user->setBirthday($faker->dateTimeBetween('-30 years', '-18 years'));

            // persist user object
            $manager->persist($user);

            $this->addReference('user-'.$i, $user);
        }
    $manager->flush();
    }
}