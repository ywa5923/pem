<?php

namespace App\Controller;

use App\Entity\WorkInterruption;
use App\Form\WorkInterruptionType;
use App\Repository\WorkInterruptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/work/interruption")
 */
class WorkInterruptionController extends AbstractController
{
    /**
     * @Route("/", name="work_interruption_index", methods={"GET"})
     */
    public function index(WorkInterruptionRepository $workInterruptionRepository): Response
    {
        return $this->render('work_interruption/index.html.twig', [
            'work_interruptions' => $workInterruptionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="work_interruption_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $workInterruption = new WorkInterruption();
        $form = $this->createForm(WorkInterruptionType::class, $workInterruption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($workInterruption);
            $entityManager->flush();

            return $this->redirectToRoute('work_interruption_index');
        }

        return $this->render('work_interruption/new.html.twig', [
            'work_interruption' => $workInterruption,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="work_interruption_show", methods={"GET"})
     */
    public function show(WorkInterruption $workInterruption): Response
    {
        return $this->render('work_interruption/show.html.twig', [
            'work_interruption' => $workInterruption,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="work_interruption_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, WorkInterruption $workInterruption): Response
    {
        $form = $this->createForm(WorkInterruptionType::class, $workInterruption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('work_interruption_index', [
                'id' => $workInterruption->getId(),
            ]);
        }

        return $this->render('work_interruption/edit.html.twig', [
            'work_interruption' => $workInterruption,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="work_interruption_delete", methods={"DELETE"})
     */
    public function delete(Request $request, WorkInterruption $workInterruption): Response
    {
        if ($this->isCsrfTokenValid('delete'.$workInterruption->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($workInterruption);
            $entityManager->flush();
        }

        return $this->redirectToRoute('work_interruption_index');
    }
}
