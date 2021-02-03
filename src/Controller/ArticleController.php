<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\UserArticle;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/{article_type}", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository,Request $request,$article_type,PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q');
        $queryBuilder = $articleRepository->getWithSearchQueryBuilder($article_type,$q);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50 /*limit per page*/
        );


        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
            'article_type'=>$article_type
        ]);
    }

    /**
     * @Route("/new/{article_type}", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request,$article_type): Response
    {

        $article=new Article();
        $form = $this->createForm(ArticleType::class,$article,[
            "article_type"=>$article_type
        ]);
        $form->handleRequest($request);

       // dump($article);exit();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();


            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Your item has been created');
            return $this->redirectToRoute('article_show',[
                'id'=>$article->getId(),
                'article_type'=>$article_type
            ]);
        }

        return $this->render('article/new.html.twig', [
            'article_type'=>$article_type,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}/{article_type}", name="article_show", methods={"GET"})
     */
    public function show(Article $article,$article_type): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'article_type'=>$article_type
        ]);
    }

    /**
     * @Route("/{id}/edit/{article_type}", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article,$article_type): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Your article has been updated');
            return $this->redirectToRoute('article_show', [
                'id' => $article->getId(),
                 'article_type'=>$article_type
            ]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'article_type'=>$article_type
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_home');
    }
}
