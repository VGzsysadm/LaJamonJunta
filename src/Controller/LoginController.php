<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Token;
use App\Form\RestoreType;
use App\Service\TokenGenerator;
use App\Service\MailGenerator;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Form\RecoveryPasswordType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginController extends AbstractController
{
    private $session;
    public function __construct()
    {
        $this->session = new Session();
    }
    /**
     * @Route("{_locale}/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('login');
        }
        else{
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login/index.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
        }
    }
    /**
     * @Route("{_locale}/recuperar-password", name="restore", methods="GET|POST")
     */
    public function PasswordRestore(Request $request, UserRepository $userrepository, TokenGenerator $tokengenerator, MailGenerator $mailgenerator, TranslatorInterface $translator)
    {
       $user = ['email' => null];
       $form = $this->createForm(RestoreType::class, $user);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $user = $form->getData();
           $email = $user['email'];
           $user = $userrepository->findOneBy(['email' => $email]);
           if ($user === null){
               $message = $translator->trans("El email no existe en nuestra base de datos o es incorrecto.");
               $this->session->getFlashBag()->add("danger", $message);
               return $this->redirectToRoute('restore');
           }
           // 2.5) Generating token
           $getprofile = $user->getID();
           $pledge = $tokengenerator->generateToken();
           $url = $this->generateUrl('restart/', 
           array(
               'token' => $pledge
           ),
           UrlGeneratorInterface::ABSOLUTE_URL
           );
           $token = $user->getToken();
           $creation = new \DateTime("now");
           $valid = (new \DateTime("now"))->modify('+1 day');
           $token->setToken($pledge);
           $token->setCreation($creation);
           $token->setValid($valid);
           $token->setActive(true);
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->persist($token);
           $entityManager->flush();
           $message = $translator->trans("Se ha enviado un email al correo.");
           $this->session->getFlashBag()->add("success", $message);
           // 6) Sending the email to the user
           $mailgenerator->lost($email, $url);
       }         
       return $this->render('login/restore.html.twig', array (
           'form' => $form->createView(),
       ));
    }
    /**
     * @Route("{_locale}/forget-password/{token}/", name="restart/")
     */
    public function passwwordrestart($token, Request $request, Token $pledge, TokenRepository $tokenrepository, MailGenerator $mailgenerator, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        //Collection
        $timestamp = new \DateTime("now");
        $random = $mailgenerator->generateRandomString();
        $entityManager = $this->getDoctrine()->getManager();
        //Validation
        $isActive = $pledge->getActive();
        $validDate = $pledge->getValid();
        if ($isActive === false){
            $message = $translator->trans("This token already used. Please request a new one.");
            $this->session->getFlashBag()->add("danger", $message);
            return $this->redirectToRoute('login');
        }  else {
            if ( $timestamp > $validDate ) {
                $pledge->setActive(false);
                $entityManager->persist($pledge);
                $entityManager->flush();
                $message = $translator->trans("This token is not valid. Please request a new one.");
                $this->session->getFlashBag()->add("danger", $message);
                return $this->redirectToRoute('login');
            } else {
                $pledge->setActive(false);
                $entityManager->persist($pledge);
                $user = $pledge->getUsername();
                $encoded = $passwordEncoder->encodePassword($user, $random);
                $user->setPassword($encoded);
                $message = $translator->trans("A new password has been sent to your email.");
                $this->session->getFlashBag()->add("success", $message);
                $entityManager->persist($user);
                $entityManager->flush();
                $mailgenerator->restore($user->getEmail(), $random);
                return $this->redirectToRoute('login');
            }                   
        }return $this->redirectToRoute('login');
    }

    /**
     * @Route("{_locale}/restore-user", name="restore-user", methods="GET|POST")
     */
    public function UserRestore(Request $request, UserRepository $userrepository, MailGenerator $mailgenerator, TranslatorInterface $translator)
    {
       $user = ['email' => null];
       $form = $this->createForm(RestoreType::class, $user);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $user = $form->getData();
           $email = $user['email'];
           $user = $userrepository->findOneBy(['email' => $email]);
           if ($user === null){
               $message = $translator->trans("El email no existe en nuestra base de datos o es incorrecto.");
               $this->session->getFlashBag()->add("danger", $message);
               return $this->redirectToRoute('restore');
           }
           // 2.5) Generating token
           $user->getUsername();
           $message = $translator->trans("Se ha enviado un email al correo.");
           $this->session->getFlashBag()->add("success", $message);
           // 6) Sending the email to the user
           $mailgenerator->username($email, $user->getUsername());
           return $this->redirectToRoute('login');
       }         
       return $this->render('login/username.html.twig', array (
           'form' => $form->createView(),
       ));
    }
}