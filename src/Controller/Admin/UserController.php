<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Form\UserType;

/**
* @Route("/admin/user", name="admin_user_")
* @IsGranted("ROLE_ADMIN")
*/
class UserController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     * @return Response
     */
    public function list()
    {
      $em = $this->getDoctrine()->getManager();
      $userRepo = $em->getRepository(User::class);
      $userList = $userRepo->findAll();
      return $this->render('admin/user/list.html.twig', [
        'userList' => $userList
      ]);
    }

    /**
     * @Route("/{slug}", name="update", methods={"GET", "POST"})
     * @return Response
     */
    public function update(Request $request, UserPasswordEncoderInterface $encoder, string $slug)
    {
      $em = $this->getDoctrine()->getManager();
      $userRepo = $em->getRepository(User::class);
      $user = $userRepo->findOneBySlug($slug);

      $form = $this->createForm(UserType::class, $user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $currentDate = new \Datetime();
          $user = $form->getData();
          $user->setSlug($user->getUsername());
          $user->setUpdatedAt($currentDate);
          $encoded = $encoder->encodePassword($user, $user->getPassword());
          $user->setPassword($encoded);
          $em->persist($user);
          $em->flush();

          $userList = $userRepo->findAll();
          return $this->redirectToRoute('admin_user_list', [
            'userList' => $userList
          ]);
      }

      return $this->render('admin/user/update.html.twig', [
          'user' => $user,
          'form' => $form->createView()
      ]);
    }
}
