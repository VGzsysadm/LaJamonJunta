<?php

namespace App\Controller;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}/informacion")
 */
class InformacionController extends AbstractController
{
    /**
     * @Route("/faq", name="faq")
     */
    public function faq(UserRepository $userrepository)
    {
        $users = $userrepository->getTOtalUsers();
        return $this->render('informacion/faq.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/proveedor", name="proveedor")
     */
    public function proveedor()
    {
        return $this->render('informacion/proveedor.html.twig');
    }

    /**
     * @Route("/participar", name="participar")
     */
    public function participar()
    {
        return $this->render('informacion/participar.html.twig');
    }

    /**
     * @Route("/politica-privacidad", name="privacidad")
     */
    public function privacidad()
    {
        return $this->render('informacion/privacidad.html.twig');
    }

    /**
     * @Route("/etiquetado-de-los-cerdos", name="etiquetado")
     */
    public function etiquetado()
    {
        return $this->render('informacion/etiquetado.html.twig');
    }

    /**
     * @Route("/dehesa", name="dehesa")
     */
    public function dehesa()
    {
        return $this->render('informacion/dehesa.html.twig');
    }

    /**
     * @Route("/montanera", name="montanera")
     */
    public function montanera()
    {
        return $this->render('informacion/montanera.html.twig');
    }

    /**
     * @Route("/denominaciones-de-origen", name="denominaciones")
     */
    public function denominaciones()
    {
        return $this->render('informacion/denominaciones.html.twig');
    }
}
