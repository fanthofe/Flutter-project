<?php

namespace App\Controller\BackOffice;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /** 
     * 
     * @Route("/", name="app_home")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response{
        return $this->redirectToRoute('admin');
    }
    
}