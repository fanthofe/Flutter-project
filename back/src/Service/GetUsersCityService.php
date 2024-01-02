<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class GetUsersCityService
{   
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get all cities name from users
     * and return it as an associative array
     * @return array
     */
    public function getAllCities() : array
    {   

        $userRepository = $this->entityManager->getRepository(User::class);
        $cities = $userRepository->getAllCities();

        $cityNames = array_map(function ($result) {
            return $result['city'];
        }, $cities);

        $cityNames = $this->organizeArray($cityNames);

        return $cityNames;
    }

    /**
     * Get all cities zip from users
     * and return it as an associative array
     * @return array
     */
    public function getAllCitiesZip() : array
    {   

        $userRepository = $this->entityManager->getRepository(User::class);
        $zips = $userRepository->getAllCitiesZip();

        $zipValues = array_map(function ($result) {
            return $result['zip'];
        }, $zips);
        
        $zipValues = $this->organizeArray($zipValues);

        return $zipValues;
    }

    /**
     * Organize array by removing duplicate values and sort values
     * and return it as an associative array
     *
     * @param array $array
     * @return array
     */
    private function organizeArray(array $array) : array
    {
        asort($array);
        $array = array_values($array);
        $array = array_combine($array, $array);

        return $array;
    }

}
