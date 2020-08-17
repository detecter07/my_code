<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\ArticlesRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/api", name="api_")
 */
class APIController extends AbstractController
{
    /**
     * @Route("/", name="api")
     */
    public function index()
    {
    }
    /**
     * @Route("/articles/all", name="liste", methods={"GET"})
     */
    public function liste(ArticleRepository $articlesRepo)
    {
        // we get the list Of Articles
        $articles = $articlesRepo->apiFindAll();

        // we specify that we use the Encoder JSON
        $encoders = [new JsonEncoder()];

        // we instanciate the ObjectNormalizer to convert  a collection to array
        $normalizers = [new ObjectNormalizer()];

        // we instanciate the Serializer
        $serializer = new Serializer($normalizers, $encoders);

        // we convert to Json Format
        $jsonContent = $serializer->serialize($articles, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        // we create new Instance
        $response = new Response($jsonContent);

        //we add  HTTP header
        $response->headers->set('Content-Type', 'application/json');

        // we send our Response
        return $response;
    }
    /**
     * @Route("/article/show/{id}", name="article", methods={"GET"})
     */
    public function getArticle(Article $article)
    {
        $encoders = [new JsonEncoder()];

        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($article, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/article/add", name="Insertion", methods={"POST"})
     */
    public function addArticle(Request $request)
    {
        // On vérifie si la requête est une requête Ajax
        if ($request->isXmlHttpRequest()) {

            $article = new Article();

            $donnees = json_decode($request->getContent());

            // On hydrate l'objet
            $article->setTitle($donnees->title);
            $article->setContent($donnees->content);
            $article->setImage($donnees->Image);
            $user = $this->getDoctrine()->getRepository(Users::class)->findOneBy(["id" => 1]);
            $article->setUsers($user);

            // we save  Data into database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            // we send the Confirmation
            return new Response('ok', 201);
        }
        return new Response('Failed', 404);
    }
    /**
     * @Route("/article/edit/{id}", name="edit", methods={"PUT"})
     */
    public function editArticle(?Article $article, Request $request)
    {
        //we test if we have  Ajax Request
        if ($request->isXmlHttpRequest()) {

            $donnees = json_decode($request->getContent());

            // we initialize the Response
            $code = 200;

            // we test if the article don't exists
            if (!$article) {

                $article = new Article();
                // we change the Response Code
                $code = 201;
            }

            $article->setTitle($donnees->title);
            $article->setContent($donnees->content);
            $article->setImage($donnees->Image);
            $user = $this->getDoctrine()->getRepository(Users::class)->find(1);
            $article->setUsers($user);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            // we return the code of confirmation
            return new Response('ok', $code);
        }
        return new Response('Failed', 404);
    }
    /**
     * @Route("/article/delete/{id}", name="delete", methods={"DELETE"})
     */
    public function removeArticle(Article $article)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return new Response('ok');
    }
}
