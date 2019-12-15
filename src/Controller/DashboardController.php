<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function dashboard()
    {
      return $this->render('dashboard.html.twig', [
      ]);
    }
}
