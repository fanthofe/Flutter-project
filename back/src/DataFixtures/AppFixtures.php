<?php

namespace App\DataFixtures;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private $connexion;

    public function __construct(Connection $connexion)
    {
        $this->connexion = $connexion;
    }

    private function truncate()
    {
        // Unactive foreign key check to make truncate command working
        // TRUNCATE set Auto Increment and Id start at 1
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE user');
        $this->connexion->executeQuery('TRUNCATE TABLE availability');
        $this->connexion->executeQuery('TRUNCATE TABLE chat');
        $this->connexion->executeQuery('TRUNCATE TABLE chat_message');
        $this->connexion->executeQuery('TRUNCATE TABLE children');
        $this->connexion->executeQuery('TRUNCATE TABLE comment');
        $this->connexion->executeQuery('TRUNCATE TABLE `order`');
        $this->connexion->executeQuery('TRUNCATE TABLE subscription');
    }

    public function load(ObjectManager $manager): void
    {
        $this->truncate();
    }
}