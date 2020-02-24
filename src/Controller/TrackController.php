<?php


namespace App\Controller;

use App\Entity\Project;
use App\Entity\Track;
use App\Form\TrackType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/{project}/track", name="track_")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @return Response
 */
class TrackController extends BaseController
{
    /**
     * Creates a new track project.
     *
     * @Route("/", name="create", methods={"GET", "POST"})
     * @ParamConverter("project", class="App:Project")
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function create(Request $request, Project $project)
    {
        $track = new Track();
        $form = $this->createForm(TrackType::class, $track);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($track);
//            $audio = $track->getAudio();
//            if (null != $audio) {
//                $fileName = $this->generateUniqueFileName().'.'.$audio->guessExtension();
//                $audio->move(
//                    $this->getParameter('project_audio_directory'),
//                    $fileName
//                );
//                $track->setAudio($fileName);
//            }
            $track->setSlug($this->slugger->slug($track->getName()));
            $track->setProject($project);
            $track->setCreatedAt(new \DateTime());
            $em->flush();

            return $this->redirectToRoute('project_detail', array('slug' => $project->getSlug()));
        }

        return $this->render('default/track/create.html.twig', array(
            'track' => $track,
            'project' =>$project,
            'form' => $form->createView(),
        ));
    }
}