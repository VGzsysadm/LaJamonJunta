<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Entity\User;
use App\Form\PollType;
use App\Form\VoteType;
use App\Repository\PollRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * @Route("/poll")
 */
class PollController extends AbstractController
{   private $session;
    public function __construct()
    {
        $this->session = new Session();
    }
    /**
     * @Route("/", name="poll_index", methods="GET")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(PollRepository $pollRepository): Response
    {
        return $this->render('poll/index.html.twig', ['polls' => $pollRepository->findAll()]);
    }

    /**
     * @Route("/new", name="poll_new", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $poll = new Poll();
        $form = $this->createForm(PollType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($poll);
            $em->flush();

            return $this->redirectToRoute('poll_index');
        }

        return $this->render('poll/new.html.twig', [
            'poll' => $poll,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/public", name="poll_public", methods="GET")
     */
    public function voteShow(PollRepository $pollRepository): Response
    {
        return $this->render('poll/public.html.twig', ['polls' => $pollRepository->findAll()]);
    }

    /**
     * @Route("/public/{id}", name="poll_vote", methods="GET|POST")
     */
    public function vote($id)
    {
    $sure = false;
    $entityManager = $this->getDoctrine()->getManager();
    $poll = $entityManager->getRepository(Poll::class)->find($id);
    $voted = $this->getUser()->getVoted();
    if (!$poll) {
        throw $this->createNotFoundException(
            'No product found for id '.$id
        );
    }
    if($voted === $sure)
    {
    $poll->setVotes($poll->getVotes()+1);
    $voted = $this->getUser()->setVoted(true);
    $entityManager->flush();
    }else{$message = "No puedes votar más de una vez.";
        $this->session->getFlashBag()->add("status", $message);
    }
    return $this->redirectToRoute('poll_public', [
        'id' => $poll->getId()
    ]);
    }
    /**
     * @Route("/{id}", name="poll_show", methods="GET")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Poll $poll): Response
    {
        return $this->render('poll/show.html.twig', ['poll' => $poll]);
    }

    /**
     * @Route("/{id}/edit", name="poll_edit", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Poll $poll): Response
    {
        $form = $this->createForm(PollType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('poll_edit', ['id' => $poll->getId()]);
        }

        return $this->render('poll/edit.html.twig', [
            'poll' => $poll,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="poll_delete", methods="DELETE")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Poll $poll): Response
    {
        if ($this->isCsrfTokenValid('delete'.$poll->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($poll);
            $em->flush();
        }

        return $this->redirectToRoute('poll_index');
    }
}
