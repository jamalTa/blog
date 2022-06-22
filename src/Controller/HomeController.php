<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('home/index.html.twig',
         [
             //Je recupere la liste des articles que j envoie a la vue
            'list_article'=>$articlesRepository->findAll()  
        ]);
    }

    #[Route('/detail-article{id}', name: 'detail_article')]
    public function show($id, ArticlesRepository $articlesRepository): Response
    {
        return $this->render('home/detail_article.html.twig',
         [
            
            'article'=>$articlesRepository->find($id)  
        ]);
    }
}
