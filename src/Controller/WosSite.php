<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;


/**
 *
 * @Route("/wos")
 */
class WosSite extends AbstractController
{

    /**
     * @Route("/index", name="wos_index", methods={"GET"})
     */
    public function index()
    {
        return $this->render("wos/index.html.twig",[]);
    }

    /**
     * @Route("/home")
     */
    public function home()
    {
        return $this->render("wos/logged.html.twig",[]);
    }

    /**
     * @Route("/article")
     */

    public function article()
    {
        return $this->render('wos/article.html.twig',[]);
    }

    /**
     * @Route("/citations")
     */
    public function citation()
    {
        return $this->render('wos/citations.html.twig');
    }

    /**
     * @Route("/exclude_types")
     */
    public function exclude_types()
    {
        return $this->render('wos/exclude_types.html.twig');
    }

    /**
     * @Route("/refine-years")
     */
    public function refineYears()
    {
        return $this->render('wos/refine-years.html.twig');
    }


    /**
     * @Route("/refine-document-type")
     */
    public function refineDocTypes()
    {
        return $this->render('wos/documentTypes.html.twig');
    }

    /**
     * @Route("/citations100")
     */
    public function citations100()
    {
        return $this->render('wos/citations100.html.twig');
    }

}
