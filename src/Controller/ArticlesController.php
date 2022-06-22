<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/articles')]
class ArticlesController extends AbstractController
{
    #[Route('/', name: 'articles_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('articles/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //je récupere le fichier passer dans le form
            $image = $form->get('image')->getdata();
            //si il ya une image de charger je créé un nom unique a cette image
            if($image)
            {
                $img_file_name = uniqid(). '.'. $image->guessExtension();
                //enregistrer le fichier dans le dossier image
                $image->move($this->getParameter('upload_dir'), $img_file_name);
                //une fois enregistrer je set l object article
                $article->setImage($img_file_name);
            }else{
                $article->setImage('imgDefault.jpg');
              
            }
            //je récupere la date du jour
            $date_jour = new DateTime();
            $date_jour->setTimezone(new DateTimeZone('Europe/Paris'));      
            //je set la propriété date_publication de l objet article d enregistrement en bdd
            $article->setDatePublication($date_jour);
            //j enregistre en bdd
            
            $article->setAuteur('admin');
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'articles_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'articles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        //je recupere l ancien nom de l image avnt qu il soit effacer
        $old_name_img = $article->getImage();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           //je récupere le fichier passer dans le form
           $image = $form->get('image')->getdata();
           //si il ya une image de charger je créé un nom unique a cette image
           if($image)
           {
               $img_file_name = uniqid(). '.'. $image->guessExtension();
               //enregistrer le fichier dans le dossier image
               $image->move($this->getParameter('upload_dir'), $img_file_name);
               //une fois enregistrer je set l object article
               $article->setImage($img_file_name);
               $name_file_delete = $this->getParameter('upload_dir') . $old_name_img;

                if (file_exists($name_file_delete)  && is_file($old_name_img)) {
                    unlink($name_file_delete);
                }
           }else{
               $article->setImage($old_name_img);
           }
           

    //-----------------envoie en dbb-------------------------------------------------
            $entityManager->flush();

            return $this->redirectToRoute('articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'articles_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $old_name_img = $article->getImage();
            if ($old_name_img != 'defaultimg.jpg') {
                $name_file_delete = $this->getParameter('upload_dir') . $old_name_img;
                if (file_exists($name_file_delete) && is_file($name_file_delete)) {
                    unlink($name_file_delete);
                }
            }
        }

        return $this->redirectToRoute('articles_index', [], Response::HTTP_SEE_OTHER);
    }
}
