<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Category;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    
    /**
     * @Route("/form", name="form_produit")
     */
    public function produitForm(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        
        $produit = new Produit;
        $form = $this->createForm(ProduitType::class, $produit);
        
        $form->handleRequest($request); //Récupère les informations du formulaire validé et les applique à l'objet Entity lié
        
        if($request->isMethod('post') && $form->isValid()){
            $duplicata = $produitRepository->findOneByName($produit->getName());
            
            if(!$duplicata){
                $entityManager->persist($produit);
                $entityManager->flush();
            }

            return $this->redirect($this->generateUrl('index'));
        }
        
        return $this->render('index/form.html.twig', array(
           'produitForm' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/edit/{idProduit}", name="produit_edit")
     */
    public function editProduit(Request $request, $idProduit){
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        
        $produit = $produitRepository->find($idProduit);
        $form = $this->createForm(ProduitType::class, $produit);
        
        $form->handleRequest($request);
        
        if($request->isMethod('post') && $form->isValid()){
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('index'));
        }
        
        return $this->render('index/form.html.twig', [
            'produitForm' => $form->createView(),
        ]);
        
    }
    
    /**
     * @Route("/delete/{idProduit}", name="produit_delete")
     */
    public function deleteProduit(Request $request, $idProduit){
	$entityManager = $this->getDoctrine()->getManager();
	$produitRepository = $entityManager->getRepository(Produit::class);
        
	$produit = $produitRepository->find($idProduit);
        
        if($produit){
            $entityManager->remove($produit);
            $entityManager->flush();
        }
        
        return $this->redirect($this->generateUrl('index'));
    }
    
    
    /**
     * @Route("/form/category", name="category_fiche")
     */
    public function categoryForm(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $entityManager->getRepository(Category::class);
        
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);
        
        if($request->isMethod('post') && $form->isValid()){
            $duplicata = $categoryRepository->findOneByName($category->getName());
            
            if(!$duplicata){
                $entityManager->persist($category);
                $entityManager->flush();
            }
            return $this->redirect($this->generateUrl('index'));
        }
        
        return $this->render('index/form.html.twig', array(
            'produitForm' => $form->createView(),
        ));
    }
}