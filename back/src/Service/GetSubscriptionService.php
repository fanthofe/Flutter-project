<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Subscription;

class GetSubscriptionService
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
     * Get all subscription names from subscription
     * and return it as an associative array
     * @return array
     */
    public function getAllSubscriptionName() : array
    {   

        $subscriptionRepository = $this->entityManager->getRepository(Subscription::class);
        $subscriptionNames = $subscriptionRepository->getAllSubscriptionsNames();
  
        $subscriptionNames = array_map(function ($result) {
            return $result['name'];
        }, $subscriptionNames);

        $subscriptionNames = $this->organizeArray($subscriptionNames);

        return $subscriptionNames;
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
