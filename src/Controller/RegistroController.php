<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profile;
use App\Entity\Token;
use App\Entity\Hierarchy;
use App\Entity\Award;

use App\Form\UserType;
use App\Service\TokenGenerator;
use App\Service\MailGenerator;
use App\Repository\TokenRepository;
use App\Repository\ProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistroController extends AbstractController
{
    private $session;
    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @Route("{_locale}/registro", name="registro")
     */
    public function registro(Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokengenerator, MailGenerator $mailgenerator, TranslatorInterface $translator)
    {
        // 1) build the form
        $user = new User();
        $profile = new Profile();
        $token = new Token();
        $award = new Award();
        $hierarchy = new Hierarchy();
        $form = $this->createForm(UserType::class, $user);
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        $email = $form['email']->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $terms = $form->get('termsAccepted');
            if ( $terms ) {
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            // 4) Submit the data!
            $pledge = $tokengenerator->generateToken();
            $terms = true;
            $token->setUsername($user);
            $award->setUser($user);
            $hierarchy->setUser($user);
            $token->setToken($pledge);
            $profile->setTerms($terms);
            $profile->setUsername($user);
            // 5) save the User!
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->persist($profile);
            $entityManager->persist($token);
            $entityManager->persist($award);
            $entityManager->persist($hierarchy);
            $entityManager->flush();
            // 6) Generating the url for activation
            $getprofile = $user->getID();
            $url = $this->generateUrl('activation/', 
            array(
                'token' => $pledge
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
            );
            // 6) Sending the email to the user
            $mailgenerator->registration($email, $url);
            $message = $translator->trans('Se ha enviado un email a tu cuenta.');
            $this->session->getFlashBag()->add("success", $message);
            return $this->redirectToRoute('login');
            }
        }
        return $this->render(
            'registro/index.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
    * @Route("/activacion/{token}/", name="activation/", methods={"GET"}))
    */
    public function activacion(Request $request, Token $pledge, $token)
    {
        $datenow = new \DateTime("now");
        $token = $request->attributes->get('token');
        $creation = $pledge->getCreation();
        $user = $pledge->getUsername();
        $valid = $pledge->getValid();
        $active = $pledge->getActive();
        if ( $active === false ) { 
            $message = "This token already used. Please request a new one.";
            $this->session->getFlashBag()->add("danger", $message);
        } else {
            if ( $datenow > $valid ) {
                $message = "This token is not valid. Please request a new one.";
                $pledge->setActive(false);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($pledge);
                $entityManager->flush();
                $this->session->getFlashBag()->add("danger", $message);
                } else {
                    $pledge->setActive(false);
                    $entityManager = $this->getDoctrine()->getManager();
                    $user->setIsActive(true);
                    $entityManager->persist($pledge);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $message = "You account has been activated.";
                    $this->session->getFlashBag()->add("success", $message);
                }
        }
        return $this->render('emails/confirmation.html.twig');
    }
}
