<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\AddCategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category')]
    public function index(CategoryRepository $categoryRepository): Response
    {

        //récuperation de la liste des categories avec la methode findall de l objet categoryrepositorie
        $list_category = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            
            //j envoie la liste a la vue
            'list_category' => $list_category
            
        ]);
    }

    #[Route('/addCategory', name: 'add_category')]
    public function newCategorie(Request $request, EntityManagerInterface $manager): Response
    {
        //creation d'un objet category vide
        $category = new Category();

        //je lie mon objet $category avec  e formulaire addCategoryType.php
        $form= $this->createForm(AddCategoryType::class, $category);

        //je met mon formulaire a l ecoute des requetes request si y a $_post present
        $form->handleRequest($request);
        //je vérifi si le formulaire et validé
        if($form->isSubmitted()&& $form->isValid())
        {
            // j enregistre les données passer par le formulaires dans la bdd
            $manager->persist($category);
            $manager->flush();
            //ensuite je redirige vers 
            return $this->redirectToRoute('category');
        }
        return $this->render('category/new_category.html.twig', [
            
        'form'=>$form->createView()
            
        ]);
    }

    //----------------------Modifier un enregistrement---------------------------------

    #[Route('/updateCategory/{id}', name: 'update_category')]
    public function updateCategorie($id,Request $request, EntityManagerInterface $manager,CategoryRepository $categoryRepository): Response
    {
        //je récupére l enregistrement pour la modifié
        $category = $categoryRepository->find($id);

        //je lie mon objet $category avec  e formulaire addCategoryType.php
        $form= $this->createForm(AddCategoryType::class, $category);

        //je met mon formulaire a l ecoute des requetes request si y a $_post present
        $form->handleRequest($request);
        //je vérifi si le formulaire et validé
        if($form->isSubmitted()&& $form->isValid())
        {
            // j enregistre les données passer par le formulaires dans la bdd
            $manager->persist($category);
            $manager->flush();
            //ensuite je redirige vers 
            return $this->redirectToRoute('category');
        }
        return $this->render('category/update_category.html.twig', [
            
        'form'=>$form->createView()
            
        ]);
    }

//--------------------------lire un enregistrement--------------------------

#[Route('/showCategory/{id}', name: 'show_category')]
public function showCategorie($id,CategoryRepository $categoryRepository): Response
{
    //je récupére l enregistrement pour la voir avec son id
    $category = $categoryRepository->find($id);

    return $this->render('category/show_category.html.twig', [
    //j envoie un enregistrement a la vie
    'category'=>$category
        
    ]);
}

//--------------------------supprimer un enregistrement--------------------------

#[Route('/deleteCategory/{id}', name: 'delete_category')]
public function deleteCategorie($id,CategoryRepository $categoryRepository, EntityManagerInterface $manager): Response
{
    //je récupére l enregistrement pour la voir avec son id
    $category = $categoryRepository->find($id);

    $manager->remove($category);
    $manager->flush();

    return $this->redirectToRoute('category');
}

}
