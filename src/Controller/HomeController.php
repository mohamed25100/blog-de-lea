<?php

namespace App\Controller;
//use class name Article from database #to use database
use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
//fin #to use database

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $article = $doctrine->getRepository(Article::class)->findall();
        return $this->render('home/index.html.twig', ['article' => $article]  );
    }
}