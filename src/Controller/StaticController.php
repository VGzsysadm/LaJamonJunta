<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/proveedores")
 */
class StaticController extends AbstractController
{
    /**
     * @Route("/public", name="proveedores")
     */
    public function index()
    {
        return $this->render('static/index.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/ibericos-vazquez", name="ibericos_vazquez")
     */
    public function prv1()
    {
        return $this->render('static/ibericosvazquez.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/beher", name="beher")
     */
    public function prv2()
    {
        return $this->render('static/beher.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/chorizo-jamon", name="chorizojamon")
     */
    public function prv3()
    {
        return $this->render('static/chorizojamon.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/embutidos-jamones-ibericos-mirabel", name="emjamirabel")
     */
    public function prv4()
    {
        return $this->render('static/emjamirabel.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/ibericos-guillen", name="ibericosguillen")
     */
    public function prv5()
    {
        return $this->render('static/ibericosguillen.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/embutidos-morato", name="embutidosmorato")
     */
    public function prv6()
    {
        return $this->render('static/embutidosmorato.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/jamonestartessos", name="jamonestartessos")
     */
    public function prv7()
    {
        return $this->render('static/jamonestartessos.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/beridico", name="beridico")
     */
    public function prv8()
    {
        return $this->render('static/beridico.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/hernandez-jimenez", name="hernandezjimenez")
     */
    public function prv9()
    {
        return $this->render('static/hernandezjimenez.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/encinares-del-sur", name="encinares")
     */
    public function prv10()
    {
        return $this->render('static/encinares.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
    /**
     * @Route("/public/pedidos", name="pedidos")
     */
    public function prv11()
    {
        return $this->render('static/pedidos.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
}
