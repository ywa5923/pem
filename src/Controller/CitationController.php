<?php

namespace App\Controller;

use App\Entity\Citation;
use App\Form\CitationType;
use App\Repository\CitationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/citation")
 */
class CitationController extends AbstractController
{
    /**
     * @Route("/", name="citation_index", methods={"GET"})
     */
    public function index(CitationRepository $citationRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q');
        $queryBuilder = $citationRepository->getWithSearchQueryBuilder($q);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50 /*limit per page*/
        );


        return $this->render('citation/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="citation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $citation = new Citation();
        $form = $this->createForm(CitationType::class, $citation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($citation);
            $entityManager->flush();

            return $this->redirectToRoute('citation_index');
        }

        return $this->render('citation/new.html.twig', [
            'citation' => $citation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="citation_show", methods={"GET"})
     */
    public function show(Citation $citation): Response
    {
        return $this->render('citation/show.html.twig', [
            'citation' => $citation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="citation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Citation $citation): Response
    {
        $form = $this->createForm(CitationType::class, $citation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('citation_index', [
                'id' => $citation->getId(),
            ]);
        }

        return $this->render('citation/edit.html.twig', [
            'citation' => $citation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="citation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Citation $citation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$citation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($citation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('citation_index');
    }
}
