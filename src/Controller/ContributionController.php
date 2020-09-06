<?php


namespace App\Controller;

use App\Entity\Contribution;
use App\Entity\Project;
use App\Entity\Track;
use App\Form\ContributionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/{project}/{track}/contribution", name="contribution_")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @return Response
 */
class ContributionController extends BaseController
{
//    /**
//     * @Route("/", name="list", methods={"GET"})
//     * @return Response
//     */
//    public function list()
//    {
//        $em = $this->getDoctrine()->getManager();
//        $contributionRepo = $em->getRepository(Contribution::class);
//        $contributionList = $contributionRepo->findAll();
//        return $this->render('default/contribution/list.html.twig', [
//            'contributionList' => $contributionList
//        ]);
//    }

    /**
     * @Route("/", name="create", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request, string $project, string $track)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $contribution = new Contribution();
        $em = $this->getDoctrine()->getManager();
        $projectRepo = $em->getRepository(Project::class);
        $project = $projectRepo->findOneBySlug($project);
        $trackRepo = $em->getRepository(Track::class);
        $track = $trackRepo->findOneBy(['slug' => $track, 'project' => $project]);
        $form = $this->createForm(ContributionType::class, $contribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contribution);
            $contribution->setSlug($this->slugger->slug($contribution->getName()));
            $contribution->setCreatedAt(new \Datetime);
            $contribution->setAuthor($this->getUser());
            $contribution->setTrack($track);
            $audio = $contribution->getAudio();
            if (null != $audio) {
                $fileName = $this->generateUniqueFileName().'.'.$audio->guessExtension();
                $audio->move(
                    $this->getParameter('project_audio_directory').'/'.$project->getId().'-'.$project->getSlug().'/contributions/',
                    $fileName
                );
                $contribution->setAudio($fileName);
            }
            $em->flush();
            return $this->redirectToRoute('project_detail', array('slug' => $project->getSlug()));
        }

        return $this->render('default/contribution/create.html.twig', array(
            'contribution' => $contribution,
            'form' => $form->createView()
        ));
    }

}
