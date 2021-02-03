<?php
/**
 * Created by PhpStorm.
 * User: ywa
 * Date: 22.02.2019
 * Time: 17:38
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashBoardController
 * @package App\Controller
 * @Route("/dashboard")
 */
class DashBoardController extends AbstractController
{

    /**
     * @return mixed
     * @Route("/", name="dashboard_home")
     */
    public function home(){

        return $this->render('dashboard/home.html.twig');

    }
}