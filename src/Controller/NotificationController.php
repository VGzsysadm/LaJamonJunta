<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Comments;
use App\Entity\Post;
use App\Repository\ActivityRepository;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Knp\Component\Pager\PaginatorInterface;

class NotificationController extends AbstractController
{
    /**
     * @Route("{_locale}/private/notification", name="notification", methods={"GET","POST"})
     */
    public function index(Request $request)
    {
        // Initializing the em
        $entityManager = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $user = $this->getUser()->getId();
            $activities = $entityManager->getRepository(Activity::class)->findBy( array('target' => $user, 'acknowledge' => 0));
            $jsonData = array();  
            $idx = 0;  
            foreach ($activities as $activity) {  
                $temp = array(
                   'username' => $activity->getUsername()->getUsername(),
                   'timestamp' => $activity->getTimestamp(),
                   'action' => $activity->getAction(), 
                );   
                $jsonData[$idx++] = $temp;
                $activity->setAcknowledge(true);
                $entityManager->persist($activity);
                $entityManager->flush();  
            }
            return new JsonResponse($jsonData); 
         } else { 
            return $this->render('base.html.twig');
         } 
    }

    /**
     * @Route("{_locale}/private/notifications/activity", name="notifications-activity", methods={"GET","POST"})
     */
    public function getActivity(Request $request, PaginatorInterface $paginator)
    {
        // Initializing the em
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $result = $entityManager->getRepository(Activity::class)->findBy( array('username' => $user ),array('id' => 'DESC'));

        // Paginate the results of the query
        $activities = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );

        return $this->render('notification/index.html.twig', [
            'activities' => $activities
        ]);
    }

    /**
     * @Route("{_locale}/private/notifications/comments", name="notifications-comments", methods={"GET","POST"})
     */
    public function getComments(Request $request, PaginatorInterface $paginator)
    {
        // Initializing the em
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $result = $entityManager->getRepository(Comments::class)->findBy( array('target' => $user),array('id' => 'DESC'));

        // Paginate the results of the query
        $activities = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );

        return $this->render('notification/comments.html.twig', [
            'comments' => $activities
        ]);
    }

    /**
     * @Route("{_locale}/private/notifications/posts", name="notifications-posts", methods={"GET","POST"})
     */
    public function getPosts(Request $request, PaginatorInterface $paginator)
    {
        // Initializing the em
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $result = $entityManager->getRepository(Post::class)->findBy( array('user' => $user),array('id' => 'DESC'));

        // Paginate the results of the query
        $posts = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );

        return $this->render('notification/posts.html.twig', [
            'posts' => $posts
        ]);
    }
}
