<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

/**
 * @Route("/user", name="user_")
 * @return Response
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{slug}", name="detail", methods={"GET"})
     * @return Response
     */
    public function detail(string $slug)
    {
      $em = $this->getDoctrine()->getManager();
      $userRepo = $em->getRepository(User::class);
      $user = $userRepo->findOneBySlug($slug);
      return $this->render('default/user/detail.html.twig', [
        'user' => $user
      ]);
    }
}
