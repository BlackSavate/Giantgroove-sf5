<?php


namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/project", name="project_")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @return Response
 */
class ProjectController extends BaseController
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
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $project->setSlug($this->slugger->slug($project->getTitle()));
            $project->setCreatedAt(new \Datetime);
            $project->setAuthor($this->getUser());
            $em->flush();
            return $this->redirectToRoute('project_detail', array('slug' => $project->getSlug()));
        }

        return $this->render('default/project/create.html.twig', array(
            'project' => $project,
            'form' => $form->createView()
        ));
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
            'project' => $project,
            'tracks' => $project->getTracks()
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="update", methods={"GET", "POST"})
     * @param string $slug
     * @param Request $request
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
