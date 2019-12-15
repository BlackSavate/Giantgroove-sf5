<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/user", name="user_")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
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

      if(!$user instanceof User) {
        return new Response(null, 404);
      }

      return $this->render('default/user/detail.html.twig', [
        'user' => $user
      ]);
    }

    /**
     * @Route("/{slug}/edit", name="update", methods={"GET", "POST"})
     * @return Response
     */
    public function update(string $slug, Request $request, UserPasswordEncoderInterface $encoder)
    {
      $em = $this->getDoctrine()->getManager();

      $userRepo = $em->getRepository(User::class);
      $user = $userRepo->findOneBySlug($slug);

      if(!$user instanceof User) {
        return new Response(null, 404);
      }

      $form = $this->createForm(UserType::class, $user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

          $currentDate = new \Datetime();
          $user = $form->getData();
          $user->setSlug($user->getUsername());
          $user->setCreatedAt($currentDate);
          $user->setUpdatedAt($currentDate);
          $user->setIsActive(true);
          $encoded = $encoder->encodePassword($user, $user->getPassword());
          $user->setPassword($encoded);
          $em->persist($user);
          $em->flush();

          return $this->redirectToRoute('user_detail', [
            'slug' => $user->getSlug()
          ]);
      }

      return $this->render('default/user/update.html.twig', [
          'user' => $user,
          'form' => $form->createView()
      ]);
    }
}
