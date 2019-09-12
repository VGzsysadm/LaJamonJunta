<?php

namespace App\Controller;


use App\Entity\Provider;
use App\Entity\User;
use App\Entity\Activity;

use App\Repository\ProviderRepository;
use App\Repository\UserRepository;
use App\Service\BotTelegram;
use App\Repository\ActivityRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Knp\Component\Pager\PaginatorInterface;

/**
* @Route("{_locale}/admin/")
*/


// action 10 - User activate
// action 11 - User desactivate
// action 12 - Provider activate
// action 13 - Provider desactivate



class AdminController extends AbstractController
{

    /**
     * @Route("dashboard", name="admin_dashboard")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function dashboard(Request $request, ActivityRepository $activityrepository, PaginatorInterface $paginator)
    {

        //GETTING UserActivity
        $result = $activityrepository->getLogs();

        // Paginate the results of the query
        $activities = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            25
        );

        return $this->render('admin/dashboard.html.twig', [
            'activities' => $activities
        ]);
    }

    /**
     * @Route("provider", name="admin_providers")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function index(ProviderRepository $providerrepository)
    {
        return $this->render('admin/index.html.twig', [
            'providers' => $providerrepository->findAll(
                array(
                    'id' => 'DSC'
                )
                ),
        ]);
    }

    /**
     * @Route("users", name="admin_users")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function users( Request $request, PaginatorInterface $paginator)
    {
        //INIT Entity Manager
        $entityManager = $this->getDoctrine()->getManager();
        //GETTING PCOMMENTS
        $result = $entityManager->getRepository(User::class)->findAll();

        // Paginate the results of the query
        $users = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            15
        );

        return $this->render('admin/users.html.twig', [
                'users' => $users,
            ]);
    }

    /**
     * @Route("provider/activate/{id}", name="admin_activate_provider", methods={"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function ActivateProvider($id, BotTelegram $telegram)
    {
        $admin = $this->getUser();
        // Initializing the em
        $entityManager = $this->getDoctrine()->getManager();
        $timestamp = new \DateTime("now");
        // Checking if is active
        $provider = $entityManager->getRepository(Provider::class)->find($id);
        $check = $provider->getActive();
        $target = null;
        $adminactivity = new Activity();
        if ( $check == 0 ){
        // Activating the provider
        $provider->setActive(true);
        $entityManager->persist($provider);
        // Sending the activity to whole users
        $targets = $entityManager->getRepository(User::class)->findAll();
        foreach ($targets as $target){
            $activity = new Activity();   
            $activity->setUsername($provider->getUser());
            $activity->setTarget($target);
            $activity->setTimestamp($timestamp);
            $activity->setProvider($provider);
            $activity->setAction(2);
            $activity->setAcknowledge(false);
            $entityManager->persist($activity);
        }
         // Setting the Activity
         $adminactivity->setUsername($admin);
         $adminactivity->setTarget($admin);
         $adminactivity->setTimestamp($timestamp);
         $adminactivity->setProvider($provider);
         $adminactivity->setAction(12);
         $adminactivity->setAcknowledge(true);
         $entityManager->persist($adminactivity);
        $entityManager->flush();
        $telegram->pushprovider($provider->GetName(), $provider->getPicture());
        //Saving data
        return $this->redirectToRoute('admin_providers');
    }
    if ( $check == 1 ) {
        // Setting the Activity
        $adminactivity->setUsername($admin);
        $adminactivity->setTarget($admin);
        $adminactivity->setTimestamp($timestamp);
        $adminactivity->setProvider($provider);
        $adminactivity->setAction(13);
        $adminactivity->setAcknowledge(true);
        $entityManager->persist($adminactivity);
        $provider->setActive(false);
        $entityManager->persist($provider);
        $entityManager->flush();
        return $this->redirectToRoute('admin_providers');
    }
    return $this->redirectToRoute('admin_providers');
    }

    /**
     * @Route("users/activate/{id}", name="admin_activate_users", methods={"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function ActivateUser($id)
    {
        $user = $this->getUser();
        // Initializing the em
        $entityManager = $this->getDoctrine()->getManager();
        $timestamp = new \DateTime("now");
        // Checking if is active
        $target = $entityManager->getRepository(User::class)->find($id);
        $check = $target->getisActive();

        if ( $check == 0 ){
        // Setting the Activity
        $activity = new Activity();
        $activity->setUsername($user);
        $activity->setTarget($target);
        $activity->setTimestamp($timestamp);
        $activity->setAction(10);
        $activity->setAcknowledge(true);
        $entityManager->persist($activity);
        // Activating the provider
        $target->setisActive(true);
        $entityManager->persist($target);
        $entityManager->flush();
        return $this->redirectToRoute('admin_users');
        }
        if ( $check == 1 ){
            // Setting the Activity
            $activity = new Activity();
            $activity->setUsername($user);
            $activity->setTarget($target);
            $activity->setTimestamp($timestamp);
            $activity->setAction(11);
            $activity->setAcknowledge(true);
            $entityManager->persist($activity);
            // Activating the provider
            $target->setisActive(false);
            $entityManager->persist($target);
            $entityManager->flush();
            return $this->redirectToRoute('admin_users');
        }

    }
}
