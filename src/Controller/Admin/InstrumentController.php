<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Instrument;
use App\Form\InstrumentType;

/**
* @Route("/admin/instrument", name="admin_instrument_")
* @IsGranted("ROLE_ADMIN")
*/
class InstrumentController extends AdminBaseController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     * @return Response
     */
    public function list()
    {
      $em = $this->getDoctrine()->getManager();
      $instrumentRepo = $em->getRepository(Instrument::class);
      $instrumentList = $instrumentRepo->findAll();
      return $this->render('admin/instrument/list.html.twig', [
        'instrumentList' => $instrumentList
      ]);
    }

    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     * @return Response
     */
    public function create(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $instrumentRepo = $em->getRepository(Instrument::class);

        $form = $this->createForm(InstrumentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentDate = new \Datetime();
            $instrument = $form->getData();
            $instrument->setSlug($instrument->getName());
            $instrument->setCreatedAt($currentDate);
            $instrument->setUpdatedAt($currentDate);
            $em->persist($instrument);
            $em->flush();

            $instrumentList = $instrumentRepo->findAll();
            return $this->redirectToRoute('admin_instrument_list', [
                'instrumentList' => $instrumentList
            ]);
        }

        return $this->render('admin/instrument/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{slug}", name="update", methods={"GET", "POST"})
     * @return Response
     */
    public function update(Request $request, string $slug)
    {
      $em = $this->getDoctrine()->getManager();
      $instrumentRepo = $em->getRepository(Instrument::class);
      $instrument = $instrumentRepo->findOneBySlug($slug);

      $form = $this->createForm(InstrumentType::class, $instrument);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $currentDate = new \Datetime();
          $instrument = $form->getData();
          $instrument->setSlug($instrument->getName());
          $instrument->setUpdatedAt($currentDate);
          $em->persist($instrument);
          $em->flush();

          $instrumentList = $instrumentRepo->findAll();
          return $this->redirectToRoute('admin_instrument_list', [
            'instrumentList' => $instrumentList
          ]);
      }

      return $this->render('admin/instrument/update.html.twig', [
          'instrument' => $instrument,
          'form' => $form->createView()
      ]);
    }

    /**
     * Deletes a instrument entity.
     *
     * @Route("/{instrument}", name="delete", methods={"DELETE"})
     * @ParamConverter("instrument", class="App:Instrument")
     */
    public function delete(Request $request, Instrument $instrument)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($instrument);
        $em->flush();
        return $this->redirectToRoute('admin_instrument_list', array());
    }
}
