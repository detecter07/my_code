<?php

namespace App\Controller;


use DateTime;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles_list")
     */
    public function index(ArticleRepository $article, Request $request, PaginatorInterface $paginator)
    {

        // get all articles ordered by created_at field
        $articles = $article->findAll();

        $articles = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('articles/all.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/articles/new", name="article_new")
     * @param $request
     * @return void
     */
    public function create(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $file = $form->get('Image')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL

                $newFilename = uniqid() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    echo "there is error while uploading" . $e;
                }
            }

            $article = $form->getData();
            $article->setCreatedAt(new DateTime());
            $article->setUsers($this->getUser());
            $article->setImage($newFilename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'article created!');

            return $this->redirectToRoute('articles_show', ['id' => $article->getId()]);
        }

        return $this->render('articles/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/articles/{id}/show", name="article_view")
     * @param $id ,$articleRepository
     * @return void
     */

    public function show($id, ArticleRepository $articleRepository)
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'no product found for the id  ' . $id
            );
        }
        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/articles/edit/{id}", name="article_edit")
     * @param $article
     * @return void
     */
    public function edit(Article  $article, Request $request)
    {
        $form = $this->createForm(ArticleFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Article updated ');
            return $this->redirectToRoute('article_list');
        }
        return $this->render('articles/edit.html.twig', [
            'articleForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/articles/comment/add", name="comment_add")
     * @param $article
     * @return void
     */
    public function AddComment(Request $request, Article $article)
    {
        $comment = new Comment();
        $article = new Article();


        $form = $this->createForm(CommentType::class, $comment);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment = $form->getData();
            $comment->setCreatedAt(new DateTime());
            $comment->setUser($this->getUser());
            $comment->getArticle($article->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'article created!');

            return $this->redirect($this->generateUrl('articles_list'));
        }

        return $this->render('articles/show.html.twig', [
            'form_comment' => $form->createView(),

        ]);
    }
}
