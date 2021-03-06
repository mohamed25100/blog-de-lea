<?php

namespace App\Controller;

//use class name Article from database #to use database//
use App\Entity\Article;
//edit form create
use App\Form\ArticleType;
//end edit form create
use Doctrine\Persistence\ManagerRegistry;
//fin #to use database

//use repository for edit
use App\Repository\ArticleRepository;
//fin use repository for edit
//edit form
//use Symfony\Component\Form\Extension\Core\Type\DateType; pour la date
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
// fin edit form

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//edit form
use Symfony\Component\HttpFoundation\Request;
// fin edit form
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="app_article")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        
        $article = $doctrine->getRepository(Article::class)->findall();
        $my_last = my_last($article);
        //dd($my_last);//to check my dd
        return $this->render('article/index.html.twig', ['my_last' => $my_last,
            'article' => $article]  );
    }
    
    /**
     * @Route("/dev/article/create", name="create_article")
     */
    public function createArticle(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $article = new Article();
        $article->setTitre('acticle 2');
        $article->setResume('article 2');
        $article->setContenu('bla bla bla bla bla');
        $article->setDate('2022-05-02');

        // tell Doctrine you want to (eventually) save the Article (no queries yet)
        $entityManager->persist($article);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$article->getId());
    }

    /**
     * @Route("/article/create", name="article_create")
     */
    public function new(Request $request,ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        // creates article object and initializes some data for this example
        $article = new Article();
        $article->setTitre('Write a blog post');
        $article->setResume('un resum??');
        $article->setContenu(my_repeat_5("lorem bachin truc "));
        $article->setDate(('2020-01-01'));

        $form = $this->createFormBuilder($article)
            ->add('Titre', TextType::class)
            ->add('Resume', TextType::class)
            ->add('Contenu', TextType::class)
            ->add('Date', TextType::class)
            //->add('Tags', TextType::class)
            ->add('save', SubmitType::class)
            ->getForm();
            
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             // ... perform some action, such as saving the article to the database
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
            
        }
        return $this->render('article/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $article = $doctrine->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }

        //return new Response('Check out this great product: '.$article->getTitre());

        // or render a template
        // in the template, print things with {{ article.titre }}
        return $this->render('article/show.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/dev/article/edit/{id}")
     */
    public function update(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }
        //here u can edit manualy your data using set:
        //$article->setContenu(my_repeat_5("my first article is cool ! "));
        $article->setTitre('wow article name!');
        $entityManager->flush();

        return $this->redirectToRoute('article_show', [
            'id' => $article->getId()
        ]);
    }

    /**
     * @Route("/article/edit/{id}")
     */
    public function reNew(Request $request,ManagerRegistry $doctrine, int $id): Response
    {
        
        $entityManager = $doctrine->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('article_show', [
                'id' => $article->getId()
            ]);
        }
        return $this->render('article/renew.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/article/delete/{id}")
     */
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('app_article');
    }
    
}


//my function to find the last index of array:
function my_last($a)
{
    return $a[count($a)-1];
}
function my_repeat_5($a){
    return str_repeat($a,5);
}