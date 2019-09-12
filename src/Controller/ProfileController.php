<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profile;
use App\Entity\Comments;
use App\Entity\Hierarchy;
use App\Entity\Activity;

use App\Repository\UserRepository;
use App\Repository\ProfileRepository;
use App\Repository\CommentsRepository;
use App\Repository\HierarchyRepository;
use App\Repository\ActivityRepository;

use App\Form\ProfileType;
use App\Form\AvatarType;
use App\Form\ChangePasswordType;
use App\Form\CommentType;
use App\Service\FileUpload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ProfileController extends AbstractController
{
    private $session;
    public function __construct()

    {
        $this->session = new Session();
    }
    /**
     * @Route("{_locale}/private/profile/{id}", name="profile")
     * @ParamConverter("id", class="App:User")
     * @Security("user.getId() == editUser.getId()")
     */
    public function index(Request $request, Fileupload $fileupload, User $editUser, Commentsrepository $commentsrepository, ActivityRepository $activityrepository)
    {
        $id = $request->attributes->get('id');
        $user = $this->getUser();
        $profile = new Profile();
        $form = $this->createForm(AvatarType::class, $profile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('avatar')['avatar'];
            $fileName = $fileupload->upload($file, $user->getiD());
            $user->getProfile()->setAvatar($fileName);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $message = "Avatar actualizado";
            $this->session->getFlashBag()->add("success", $message);
        }
        return $this->render('profile/index.html.twig',
        array('form' => $form->createView(),
        'comments' => $commentsrepository->findBy(
            array(
                'target' => $id
            ),
            array('id' => 'DESC'),
            10, 0
        ),
        'activities' => $activityrepository->findBy(
            array(
                'username' => $user
            ),
            array('id' => 'DESC'),
            10, 0
        ),
        )
        );
    }
    /**
     * @Route("{_locale}/private/profile/{id}/edit", name="profile-edit")
     * @ParamConverter("id", class="App:User")
     * @Security("user.getId() == editUser.getId()")
     */
    public function profileEdit(Request $request, User $editUser)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $message = "Información actualizada";
            $this->session->getFlashBag()->add("success", $message);
            return $this->redirectToRoute('profile', ['id' => $this->getUser()->getId()]);
        }
        return $this->render('profile/edit.html.twig',
        array('form' => $form->createView()
        )
        );
    }
    /**
     * @Route("{_locale}/private/profile/password/{id}", name="edit_password",methods="GET|POST")
     * @ParamConverter("id", class="App:User")
     * @Security("user.getId() == editUser.getId()")
     */
    public function editPassword(Request $request, User $editUser, UserPasswordEncoderInterface $passwordEncoder)
    {
        $editUser = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $editUser);
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                // 3) Encode the password (you could also do this via Doctrine listener)
                $password = $passwordEncoder->encodePassword($editUser, $editUser->getPlainPassword());
                $editUser->setPassword($password);
                // 4) save the User!
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($editUser);
                $entityManager->flush();
                $message = "The password has been updated.";
                $this->session->getFlashBag()->add("status", $message);
                return $this->redirectToRoute('indice');
            }
        return $this->render('profile/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{_locale}/private/profiles/{id}", name="profiles", methods={"GET","POST"})
     */
    public function profiles( Request $request, UserRepository $userrepository, CommentsRepository $commentsrepository, HierarchyRepository $hierarchyrepository, ActivityRepository $activityrepository)
    {
        $activity = new Activity();
        $user = $this->getUser();
        $timestamp = new \DateTime("now");
        $comment = new Comments();
        $id = $request->attributes->get('id');
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $find = $entityManager->getRepository(Hierarchy::class)->find($user->getId());
        if ( $find == null) {
            $hierarchy = new Hierarchy;
            $hierarchy->setUser($user);
        } else {
            $hierarchy = $find;
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $target = $userrepository->findOneBy(array('id' => $id));
            $hierarchy->setScore($hierarchy->getScore()+1);
            $activity->setUsername($user);
            $activity->setAction(1);
            $activity->setTarget($target);
            $activity->setTimestamp($timestamp);
            $activity->setAcknowledge(false);
            $comment->setUser($user);
            $comment->setTarget($target);
            $comment->setTimestamp($timestamp);
            $entityManager->persist($comment);
            $entityManager->persist($hierarchy);
            $entityManager->persist($activity);
            $entityManager->flush();
            return $this->redirect($request->getUri());
        }
        return $this->render('profile/profiles.html.twig', [ 'user' => $userrepository->findOneBy(array('id' => $id)),
        'form' => $form->createView(),
        'comments' => $commentsrepository->findBy(
            array(
                'target' => $id
            ),
            array('id' => 'DESC'),
            10, 0
        ),
        'activities' => $activityrepository->findBy(
            array(
                'username' => $id
            ),
            array('id' => 'DESC'),
            10, 0
        ),
        ]
        );
    }
}