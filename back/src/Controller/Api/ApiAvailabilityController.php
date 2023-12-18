<?php

namespace App\Controller\Api;

use App\Repository\AvailabilityRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiAvailabilityController extends AbstractController
{
     /** 
     * Get all availabilities for one user 
     * @Route("/api/availabilities/user/{userId}", name="api_user_availabilities")
     */
    public function getUserAvailabilities($userId, UserRepository $userRepository ,AvailabilityRepository $availabilityRepository)
    {   
        // find the user by id using param converter
        $user = $userRepository->find($userId);
        // find the user availabilities
        $userAvailabilities = $availabilityRepository->findBy(['user' => $user]);
        // store the availabilities in an array
        $availabilities = [];
        // loop through the availabilities
        foreach($userAvailabilities as $availability){
            // store each availability data in the array (add more data here if needed for front-end display)
            $availabilities[] = [
                'id' => $availability->getId(),
                'start' => $availability->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $availability->getEndDate()->format('Y-m-d H:i:s'),
                'user_id' => $availability->getUser()->getId(),
            ];
        }
        // return a json response with the uer availabilities
        return $this->json($availabilities, 200, []);
    }
}