<?php
// src/Controller/ArticleController.php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class ArticleController extends AbstractController  // Extend AbstractController
{

    #[Route("/articles", name:"article_index")]
    public function index(PersistenceManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(['accepted' => true]);
        return $this->render('index.html.twig', ['contents' => $articles]);
    }

    #[Route("/articles/check", name:"article_check")]
    public function check(PersistenceManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findBy(['accepted' => false]);

        return $this->render('checkinterface.html.twig', ['contents' => $articles]);
    }
    #[Route("/articles/check/{id}", name:"article_check_one")]
    public function checkone(Article $article): Response
    {
        return $this->render('check.html.twig', ['content' => $article]);
    }
    #[Route("/article/{id}/toggle-accept", name: "article_toggle_accept")]
    public function toggleAccept(Article $article, PersistenceManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $article->setAccepted(true);
        $entityManager->persist($article);
        $entityManager->flush();
        $this->addFlash('success', sprintf('Article %s has been %saccepted.', $article->getId(), $article->isAccepted() ? '' : 'un'));
        return $this->redirect('/articles/check');
    }
    #[Route("/article/{id}/delete", name: "article_delete")]
    public function delete(Article $article, PersistenceManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        $this->addFlash('success', 'Article deleted successfully.');
        return $this->redirect('/articles/check');
    }
    #[Route("/articles/create", name:"article_create")]
    public function create(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/articles/{id}", name:"article_show")]
    public function show(Article $article): Response
    {
        return $this->render('show.html.twig', ['content' => $article]);
    }

}
