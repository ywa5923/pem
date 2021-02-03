<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;


class AdminUtilityController extends AbstractController
{
    /**
     * @Route("/admin/user_api/users", methods="GET", name="get_user_api")
     *
     */

    public function getUsersApi(UserRepository $userRepository,Request $request)
    {

        $users=$userRepository
            ->getWithSearchQueryBuilder($request->query->get('query'),10)
            ->getQuery()
            ->execute();


        return $this->json([
            'users'=>$users
        ],200,[],['groups'=>['main']]
        );
    }
}