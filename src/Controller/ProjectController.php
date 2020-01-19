<?php


namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @Route("/project", name="project_")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @return Response
 */
class ProjectController extends AbstractController
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
        return $this->render('default/project/list.html.twig', [
            'projectList' => $projectList
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     * @return Response
     */
    public function create(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $currentDate = new \Datetime();
            $project = $form->getData();
            $slugger = new AsciiSlugger();
            $project->setSlug($slugger->slug($project->getTitle()));
            $project->setAuthor($this->getUser());
            $project->setCreatedAt($currentDate);
            $project->setUpdatedAt($currentDate);

            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_detail', ['slug' => $project->getSlug()]);
        }

        return $this->render('default/project/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{slug}", name="detail", methods={"GET"})
     * @param string $slug
     * @return Response
     */
    public function detail(string $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $projectRepo = $em->getRepository(Project::class);
        $project = $projectRepo->findOneBySlug($slug);

        if(!$project instanceof Project) {
            return new Response(null, 404);
        }

        return $this->render('default/project/detail.html.twig', [
            'project' => $project
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="update", methods={"GET", "POST"})
     * @return Response
     */
    public function update(string $slug, Request $request)
    {
      $em = $this->getDoctrine()->getManager();

      $projectRepo = $em->getRepository(Project::class);
      $project = $projectRepo->findOneBySlug($slug);

      if(!$project instanceof Project) {
        return new Response(null, 404);
      }

      $form = $this->createForm(ProjectType::class, $project);
      $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $project->setTitle($project->getTitle());
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_detail', [
                'slug' => $project->getSlug()
            ]);
        }

      return $this->render('default/project/update.html.twig', [
          'project' => $project,
          'form' => $form->createView()
      ]);
    }

}
