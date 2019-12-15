<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function homepage()
    {
      return $this->render('homepage.html.twig', [
      ]);
    }
}
