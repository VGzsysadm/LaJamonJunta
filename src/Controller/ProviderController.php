<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Entity\Offer;
use App\Entity\Ocomment;
use App\Repository\ProviderRepository;
use App\Repository\OfferRepository;
use App\Repository\OcommentRepository;
use App\Form\ProviderType;
use App\Form\OfferType;
use App\Form\OcommentType;


use App\Entity\Award;
use App\Entity\Hierarchy;
use App\Entity\Activity;

use App\Entity\User;
use App\Repository\UserRepository;

Use App\Service\DocumentUpload;

use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Contracts\Translation\TranslatorInterface;


class ProviderController extends AbstractController
{
    private $session;
    public function __construct()

    {
        $this->session = new Session();
    }

    /**
     * @Route("{_locale}/private/provider/", name="provider")
     */
    public function index(ProviderRepository $providerrepository, Request $request, PaginatorInterface $paginator)
    {
        // Initializing the em
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $result = $entityManager->getRepository(Provider::class)->findBy( array('active' => true ),array('id' => 'DESC'));

        // Paginate the results of the query
        $providers = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );

        return $this->render('provider/index.html.twig', [
            'providers' => $providers
        ]);
    }
    
    /**
     * @Route("{_locale}/private/provider/new", name="add_provider", methods={"GET","POST"})
     */
    public function addProvider(Request $request, UserRepository $userrepository, DocumentUpload $documentupload, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $provider = new Provider();
        $form = $this->createForm(ProviderType::class, $provider);
        $entityManager = $this->getDoctrine()->getManager();
        $findhierarchy = $entityManager->getRepository(Hierarchy::class)->find($user->getId());
        $findaward = $entityManager->getRepository(Award::class)->find($user->getId());
        $form->handleRequest($request);

        if ( $findhierarchy == null) {
            $hierarchy = new Hierarchy;
            $hierarchy->setUser($user);
        } else {
            $hierarchy = $findhierarchy;
        }
        if ( $findaward == null) { 
        $award = new Award;
        $award->setUser($user);
        } else {
            $award = $findaward;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('provider')['documentone'];
            $picture = $request->files->get('provider')['picture'];
            if ( $file !== null ) {
                $fileName = $documentupload->upload($file, $provider->getName());
                $provider->setDocumentone($fileName);
            }
            $check = $provider->getDescription();
            if ( $check == null ) {
                $empty = "Description288";
                $provider->setDescription($empty);
            }
            $pictureName = $documentupload->uploadPicture($picture, $provider->getName());
            $provider->setPicture($pictureName);   
            $hierarchy->setScore($hierarchy->getScore()+200);
            $award->setTrophy($award->getTrophy()+1);
            $provider->setUser($user);
            $provider->setActive(false);
            $entityManager->persist($provider);
            $entityManager->persist($award);
            $entityManager->persist($hierarchy);
            $message = $translator->trans('El proveedor se ha creado correctamente, rellana las ofertas a través de Mis proveedores para que los administradores lo puedan validar.');
            $this->session->getFlashBag()->add("success", $message);
            $entityManager->flush();
            return $this->redirectToRoute('provider');
        }
        return $this->render('provider/new.html.twig',
        array('form' => $form->createView())
        );
    }

    /**
     * @Route("{_locale}/private/provider/{id}", name="show_provider", methods={"GET","POST"})
     */

    public function show(Provider $provider, ProviderRepository $providerrepository, Request $request, $id, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $ocomment = new Ocomment();
        $form = $this->createForm(OcommentType::class, $ocomment);
        $form->handleRequest($request);


        //GETTING OCOMMENTS
        $result = $entityManager->getRepository(Ocomment::class)->findBy( array('provider' => $provider ),array('id' => 'ASC'));

        // Paginate the results of the query
        $ocomments = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $provider = $providerrepository->findOneBy(array('id' => $id));
            $datenow = new \DateTime("now");
            $check = $ocomment->getDescription();
            if ( $check == null ) {
                $empty = "Undefined288";
                $pcomment->setDescription($empty);
            }
            $ocomment->setUser($user);
            $ocomment->setProvider($provider);
            $ocomment->setTimestamp($datenow);
            $entityManager->persist($ocomment);
            $entityManager->flush();

            return $this->redirectToRoute('show_provider', array(
                'id' => $id
            ));
        }

        $provider->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $offer = $entityManager->getRepository(Offer::class)->findBy( array('provider' => $provider ),array('id' => 'ASC'));
        return $this->render('provider/show.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
            'ocomments' => $ocomments,
            'offers' => $offer,
        ]);
    }

    /**
     * @Route("{_locale}/private/provider/{id}/edit", name="edit_provider", methods={"GET","POST"})
     * @ParamConverter("provider", class="App:Provider")
     * @Security("user.getId() == editProvider.getUser().getId()")
     */

    public function edit(Request $request, Provider $provider, Provider $editProvider, DocumentUpload $documentupload)
    {
        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('provider')['documentone'];
            $picture = $request->files->get('provider')['picture'];
            if ( $file !== null ) {
                $fileName = $documentupload->upload($file, $provider->getName());
                $provider->setDocumentone($fileName);
            }
            $pictureName = $documentupload->uploadPicture($picture, $provider->getName());
            $provider->setPicture($pictureName);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('show_provider', array(
                'id' => $provider->getId()
            ));
        }

        return $this->render('provider/edit.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{_locale}/private/provider/{id}/offer/new", name="offer_new", methods={"GET","POST"})
     * @ParamConverter("provider", class="App:Provider")
     * @Security("user.getId() == editProvider.getUser().getId()")
     */
    public function offerNew(Request $request, Provider $editProvider, ProviderRepository $providerrepository, DocumentUpload $documentupload, $id)
    {
        $user = $this->getUser();
        $offer = new Offer();
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);
        $provider = $providerrepository->findOneBy(array('id' => $id));

        if ($form->isSubmitted() && $form->isValid()) {
            $timestamp = new \DateTime("now");
            $entityManager = $this->getDoctrine()->getManager();
            $file = $request->files->get('offer')['offerpicture'];
            if ( $file !== null ) {
                $fileName = $documentupload->offerPicture($file, $provider->getId(), $timestamp->getTimestamp());
                $offer->setOfferpicture($fileName);
            } else {
                $offer->setOfferpicture('defaultoffer.png');
            }
            $check = $offer->getDescription();
            if ( $check == null ) {
                $empty = "Undefined288";
                $offer->setDescription($empty);
            }
            $offer->setUser($user);
            $offer->setProvider($provider);
            $entityManager->persist($offer);
            $entityManager->flush();

            return $this->redirectToRoute('show_provider', array(
                'id' => $editProvider->getId()
            ));
        }

        return $this->render('provider/newoffer.html.twig', [
            'form' => $form->createView(),
            'provider' => $provider,
        ]);
    }

    /**
     * @Route("{_locale}/private/offer/{id}/edit", name="edit_offer", methods={"GET","POST"})
     * @ParamConverter("offer", class="App:Offer")
     * @Security("user.getId() == editOffer.getUser().getId()")
     */
    public function offerEdit(Request $request, Offer $editOffer, DocumentUpload $documentupload)
    {
        $form = $this->createForm(OfferType::class, $editOffer);
        $form->handleRequest($request);
        $file = $request->files->get('offer')['offerpicture'];
         if ($form->isSubmitted() && $form->isValid()) {
            if ( $file !== null ) {
                $timestamp = new \DateTime("now");
                $fileName = $documentupload->offerPicture($file, $editOffer->getProvider()->getId(), $timestamp->getTimestamp());
                $editOffer->setOfferpicture($fileName);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('show_provider', array(
                'id' => $editOffer->getProvider()->getId()
            ));
        }

        return $this->render('provider/editoffer.html.twig', [
            'offer' => $editOffer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{_locale}/private/offer/{id}/delete", name="delete_offer", methods={"DELETE"})
     * @ParamConverter("offer", class="App:Offer")
     * @Security("user.getId() == editOffer.getUser().getId()")
     */
    public function pcommentDelete(Request $request, Offer $editOffer, Offer $offer)
    {
        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('show_provider', array(
            'id' => $offer->getProvider()->getId()
        ));
    }

    /**
     * @Route("{_locale}/private/provider/comment/{id}", name="ocomment_edit", methods={"GET","POST"})
     * @ParamConverter("Ocomment", class="App:Ocomment")
     * @Security("user.getId() == editComment.getUser().getId()")
     */
    public function commentEdit(Request $request, Ocomment $editComment, Ocomment $ocomment)
    {
        $form = $this->createForm(OcommentType::class, $ocomment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datenow = new \DateTime("now");
            $ocomment->setModified($datenow);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('show_provider', array(
                'id' => $ocomment->getProvider()->getId()
            ));
        }

        return $this->render('provider/edit_comment.html.twig', [
            'ocomment' => $ocomment,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("{_locale}/private/myproviders", name="my_provider", methods={"GET","POST"})
     */
    public function myProviders()
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $providers = $entityManager->getRepository(Provider::class)->findBy( array('user' => $user ),array('id' => 'ASC'));

        return $this->render('provider/my_providers.html.twig', [
            'providers' => $providers
        ]);
    }
}