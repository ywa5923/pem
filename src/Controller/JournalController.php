<?php

namespace App\Controller;

use App\Entity\Journal;
use App\Form\JournalType;
use App\Repository\JournalRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/journal")
 */
class JournalController extends AbstractController
{
    /**
     * @Route("/", name="journal_index", methods={"GET"})
     */
    public function index(JournalRepository $journalRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $q=$request->query->get('q');

        $queryBuilder = $journalRepository->getWithSearchQueryBuilder($q);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50 /*limit per page*/
        );
        return $this->render('journal/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="journal_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $journal = new Journal();
        $form = $this->createForm(JournalType::class, $journal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($journal);
            $entityManager->flush();

            return $this->redirectToRoute('journal_index');
        }

        return $this->render('journal/new.html.twig', [
            'journal' => $journal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="journal_show", methods={"GET"})
     */
    public function show(Journal $journal): Response
    {
        return $this->render('journal/show.html.twig', [
            'journal' => $journal,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="journal_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Journal $journal): Response
    {
        $form = $this->createForm(JournalType::class, $journal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('journal_index', [
                'id' => $journal->getId(),
            ]);
        }

        return $this->render('journal/edit.html.twig', [
            'journal' => $journal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="journal_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Journal $journal): Response
    {
        if ($this->isCsrfTokenValid('delete'.$journal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($journal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('journal_index');
    }
}
