<?php

namespace App\Controller\Admin;

use App\Service\StatsService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticsController extends AbstractController
{   
    /**
     * @Route("/admin/statistics", name="admin_statistics")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(StatsService $stats, ContainerInterface $container): Response
    {   
        return $this->render('admin/statistics/index.html.twig', [
            'mercureHubUrl' => $container->getParameter('mercure_hub_url'),
            'usersCount' => $stats->countUsers(),
            'parentsCount' => $stats->countParents(),
            'gardiensCount' => $stats->countGardians(),
            'childrenCount' => $stats->countChildren(),
            'girlsCount' => $stats->countFemaleChildren(),
            'girlsPercent' => $stats->childrenPercentage()['female_percentage'],
            'boysCount' => $stats->countMaleChildren(),
            'boysPercent' => $stats->childrenPercentage()['male_percentage'],
            'chatsCount' => $stats->countChats(),
            'messagesCount' => $stats->countMessages(),
            'messagesPerUser' => $stats->messagesPerUser(),
            'messagesPerParent' => $stats->messagesPerPerent(),
            'messagesPerGardian' => $stats->messagesPerGardian(),
            'gardiansPerChild' => $stats->gardiansPerChild(),
            'messagesPerChat' => $stats->messagesPerChat(),
            'commentsCount' => $stats->countComments(),
            'commentsOnParentsCount' => $stats->countCommentsOnParents(),
            'commentsOnGardiansCount' => $stats->countCommentsOnGuardians(),
            'userCommentsAvg' => $stats->userCommentsAvarage()['avg'],
            'usersCities' => $stats->countUsersByCity(),
            'suspendedUsersCount' => $stats->countSuspendedUsers(),
        ]);
    }
}
