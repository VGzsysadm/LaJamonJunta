<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/faq")
 */
class FaqController extends AbstractController
{
    /**
     * @Route("/", name="faq")
     */
    public function index()
    {
        return $this->render('faq/index.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
}
