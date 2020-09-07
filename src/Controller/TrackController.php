<?php


namespace App\Controller;

use App\Entity\Project;
use App\Entity\Track;
use App\Form\TrackType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @param Request $request
     * @param string $project
     * @return Response
     */
    public function create(Request $request, string $project)
    {
        $em = $this->getDoctrine()->getManager();
        $projectRepo = $em->getRepository(Project::class);
        $project = $projectRepo->findOneBySlug($project);
        $track = new Track();
        $form = $this->createForm(TrackType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($track);
            $user = $this->getUser();
            $audio = $track->getAudio();
            if (!$track->getIsOpen()) {
                if (null != $audio ) {
                    $fileName = $this->generateUniqueFileName().'.'.$audio->guessExtension();
                    $audio->move(
                        $this->getParameter('project_audio_directory').'/'.$project->getId().'-'.$project->getSlug(),
                        $fileName
                    );
                    $track->setAudio($fileName);
                }
                else {
                    $this->addFlash('error', 'La piste doit soit être ouverte aux contributions, soit comporter un fichier audio valide');
                    return $this->render('default/track/create.html.twig', array(
                        'track' => $track,
                        'project' => $project,
                        'form' => $form->createView(),
                    ));
                }
            }
            else {
                $track->setAudio(null);
            }

            $track->setSlug($this->slugger->slug($track->getName()));
            $track->setProject($project);
            $track->setAuthor($user);
            $track->setStartTime(0);
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

    /**
     * Deletes a track entity.
     *
     * @Route("/{track}", name="delete")
     * @ParamConverter("track", class="App:Track")
     * @Method("DELETE")
     */
    public function delete(Request $request, Track $track)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($track);
        $em->flush();
        return $this->redirectToRoute('project_detail', array('slug' => $track->getProject()->getSlug()));
    }

}