<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Pcomments;
use App\Form\PostType;
use App\Form\PcommentType;
use App\Repository\PostRepository;
use App\Repository\PcommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Knp\Component\Pager\PaginatorInterface;

class PostController extends AbstractController
{
    /**
     * @Route("{_locale}/private/post", name="post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findBy(
                array(
                    'pinned' => false
                ),
                array('id' => 'DESC'),
                5, 0
            ),
            'pinneds' => $postRepository->findBy(
                array(
                    'pinned' => true
                ),
                array('id' => 'DESC'),
                5, 0
            ),
        ]);
    }

    /**
     * @Route("{_locale}/private/post/new", name="post_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $datenow = new \DateTime("now");
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $check = $post->getPosted();
            if ( $check == null ) {
                $empty = "Undefined288";
                $post->setPosted($empty);
            }
            $post->setUser($user);
            $post->setPinned(false);
            $post->setTimestamp($datenow);
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{_locale}/private/post/{id}", name="post_show", methods={"GET","POST"})
     */
    public function show(Post $post, Request $request, PostRepository $postrepository, $id, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //GETTING PCOMMENTS
        $result = $entityManager->getRepository(Pcomments::class)->findBy( array('post' => $post ),array('id' => 'ASC'));

        // Paginate the results of the query
        $pcomments = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );


        $pcomment = new Pcomments();
        $form = $this->createForm(PcommentType::class, $pcomment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $postrepository->findOneBy(array('id' => $id));
            $datenow = new \DateTime("now");
            $check = $pcomment->getDescription();
            if ( $check == null ) {
                $empty = "Undefined288";
                $pcomment->setDescription($empty);
            }
            $pcomment->setUser($user);
            $pcomment->setPost($post);
            $pcomment->setTimestamp($datenow);
            $entityManager->persist($pcomment);
            $entityManager->flush();

            return $this->redirectToRoute('post_show', array(
                'id' => $id
            ));
        }
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'pcomments' => $pcomments,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("{_locale}/private/post/{id}/pinned", name="post_pinned", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function pinned(Request $request, Post $post, PostRepository $postrepository): Response
    {
        $id = $request->attributes->get('id');
        $pinned = $postrepository->findOneBy(array('id' => $id));
        $check = $pinned->getPinned();
        $entityManager = $this->getDoctrine()->getManager();
        if ( $check == false ) {
            $pinned->setPinned(true);
        } else {
            $pinned->setPinned(false);
        }
        $entityManager->persist($pinned);
        $entityManager->flush();

        return $this->redirectToRoute('post_show', array(
            'id' => $post->getId()
        ));
    }

    /**
     * @Route("{_locale}/private/post/{id}/edit", name="post_edit", methods={"GET","POST"})
     * @ParamConverter("post", class="App:Post")
     * @Security("user.getId() == editPost.getUser().getId()")
     */
    public function edit(Request $request, Post $editPost, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datenow = new \DateTime("now");
            $post->setModified($datenow);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_show', array(
                'id' => $post->getId()
            ));
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{_locale}/private/post/{id}", name="post_delete", methods={"DELETE"})
     * @ParamConverter("post", class="App:Post")
     * @Security("user.getId() == editPost.getUser().getId()")
     */
    public function delete(Request $request,Post $editPost, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("{_locale}/private/post/comment/{id}", name="pcomment_edit", methods={"GET","POST"})
     * @ParamConverter("Pcomment", class="App:Pcomments")
     * @Security("user.getId() == editComment.getUser().getId()")
     */
    public function commentEdit(Request $request, Pcomments $editComment, Pcomments $pcomment): Response
    {
        $form = $this->createForm(PcommentType::class, $pcomment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datenow = new \DateTime("now");
            $pcomment->setModified($datenow);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_show', array(
                'id' => $pcomment->getPost()->getId()
            ));
        }

        return $this->render('post/edit_comment.html.twig', [
            'pcomment' => $pcomment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{_locale}/private/post/comment/{id}", name="pcomment_delete", methods={"DELETE"})
     * @ParamConverter("Pcomment", class="App:Pcomments")
     * @Security("user.getId() == editComment.getUser().getId()")
     */
    public function pcommentDelete(Request $request, Pcomments $editComment, Pcomments $pcomment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pcomment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pcomment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_show', array(
            'id' => $pcomment->getPost()->getId()
        ));
    }

    /**
     * @Route("{_locale}/private/fixed", name="fixed_posts", methods={"GET"})
     */
    public function fixedPost(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        //GETTING PCOMMENTS
        $result = $entityManager->getRepository(Post::class)->findBy( array('pinned' => true ),array('id' => 'DESC'));

        // Paginate the results of the query
        $posts = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );

        return $this->render('post/fixed-show.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("{_locale}/private/posts", name="post_posts", methods={"GET"})
     */
    public function PostsPosted(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        //GETTING PCOMMENTS
        $result = $entityManager->getRepository(Post::class)->findBy( array('pinned' => false ),array('id' => 'DESC'));

        // Paginate the results of the query
        $posts = $paginator->paginate(
            // Doctrine Query, not results
            $result,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );

        return $this->render('post/posts-show.html.twig', [
            'posts' => $posts
        ]);
    }
}
