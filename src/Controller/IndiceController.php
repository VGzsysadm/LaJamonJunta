<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Repository\ProviderRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndiceController extends AbstractController
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @Route("/", name="index")
     */
    public function base()
    {
        return $this->redirectToRoute('indice');
    }

    public function index(Request $request, ProviderRepository $providerrepository)
    {
        if( $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
        $user = $this->getUser();
        $myIp = $request->getClientIp();
        $datenow = new \DateTime("now");
        $user->getProfile()->setIp($myIp);
        $user->getProfile()->setLastLogin($datenow);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $result6 = $providerrepository->getRandomProvider(2);
        $result3 = $providerrepository->getRandomProvider(3);
        return $this->render('indice/index.html.twig', [
            'providers6' => $result6,
            'providers3' => $result3
        ]);
    }

    /**
     * @Route("/es/", name="es")
     */
    public function es(Request $request)
    {
        $request->getSession()->set('_locale', 'es');
        return $this->redirectToRoute('index');
    }
    /**
     * @Route("/ca/", name="ca")
     */
    public function ca(Request $request)
    {
        $request->getSession()->set('_locale', 'ca');
        return $this->redirectToRoute('index');
    }
    /**
     * @Route("/va/", name="va")
     */
    public function va(Request $request)
    {
        $request->getSession()->set('_locale', 'va');
        return $this->redirectToRoute('index');
    }
    /**
     * @Route("/ba/", name="ba")
     */
    public function ba(Request $request)
    {
        $request->getSession()->set('_locale', 'ba');
        return $this->redirectToRoute('index');
    }
    /**
     * @Route("/eu/", name="eu")
     */
    public function eu(Request $request)
    {
        $request->getSession()->set('_locale', 'eu');
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/gl/", name="gl")
     */
    public function gl(Request $request)
    {
        $request->getSession()->set('_locale', 'gl');
        return $this->redirectToRoute('index');
    }
    /**
     * @Route("/oc/", name="oc")
     */
    public function oc(Request $request)
    {
        $request->getSession()->set('_locale', 'oc');
        return $this->redirectToRoute('index');
    }
}
