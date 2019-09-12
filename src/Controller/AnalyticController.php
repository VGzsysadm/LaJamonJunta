<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Knp\Component\Pager\PaginatorInterface;

/**
* @Route("{_locale}/private/")
*/
class AnalyticController extends AbstractController
{
    /**
    * @Route("analytic/users", name="get_users")
    */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        // Initializing the em
        $entityManager = $this->getDoctrine()->getManager();
        $result = $entityManager->getRepository(User::class)->findAll();
        // Paginate the results of the query
        $users = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            20
        );
        return $this->render('analytic/get_users.html.twig', [
            'users' => $users
        ]);
    }
}
