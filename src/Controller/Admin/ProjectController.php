<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Project;
use App\Form\ProjectType;

/**
* @Route("/admin/project", name="admin_project_")
* @IsGranted("ROLE_ADMIN")
*/
class ProjectController extends AdminBaseController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     * @return Response
     */
    public function list()
    {
      $em = $this->getDoctrine()->getManager();
      $projectRepo = $em->getRepository(Project::class);
      $projectList = $projectRepo->findAll();
      return $this->render('admin/project/list.html.twig', [
        'projectList' => $projectList
      ]);
    }

    /**
     * @Route("/{slug}", name="update", methods={"GET", "POST"})
     * @return Response
     */
    public function update(Request $request, string $slug)
    {
      $em = $this->getDoctrine()->getManager();
      $projectRepo = $em->getRepository(Project::class);
      $project = $projectRepo->findOneBySlug($slug);

      $form = $this->createForm(ProjectType::class, $project);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $currentDate = new \Datetime();
          $project = $form->getData();
          $project->setSlug($project->getProjectname());
          $project->setUpdatedAt($currentDate);
          $project->setPassword($encoded);
          $em->persist($project);
          $em->flush();

          $projectList = $projectRepo->findAll();
          return $this->redirectToRoute('admin_project_list', [
            'projectList' => $projectList
          ]);
      }

      return $this->render('admin/project/update.html.twig', [
          'project' => $project,
          'form' => $form->createView()
      ]);
    }
}
