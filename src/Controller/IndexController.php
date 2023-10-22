<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Category;
use App\Form\ProduitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $listeCategory = $categoryRepository->findAll();
        
        $listeProduit = $produitRepository->findAll();
        shuffle($listeProduit);
        $listeProduit = array_slice($listeProduit, 0,3);
        
        return $this->render('index/accueil.html.twig', array(
            'listeProduit' => $listeProduit,
            'listeCategory' => $listeCategory,
        ));
    }
    
    /**
     * @Route("/index", name="index_all")
     */
    public function indexShuffle(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $listeCategory = $categoryRepository->findAll();
        
        $listeProduit = $produitRepository->findAll();
        
        
        
        return $this->render('index/index.html.twig', [
            'listeProduit' => $listeProduit,
            'listeCategory' => $listeCategory,
        ]);
    }
    
    /**
     * @Route("/clone/{name}", name="index_clone")
     */
    public function indexClone(Request $request, $name = "Inconnu"): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        
        $listeProduit = $produitRepository->findByCategory($name);
        
        return $this->render('index/index.html.twig', array(
            'name' => ($name . "_clone"),
            'listeProduit' => $listeProduit,
        ));
    }
    
    /**
     * @Route("/theme/{color}", name="index_thematics")
     */
    public function indexThematics(Request $request, $color = "black"){
        
        return $this->render('index/index.html.twig', array(
            "color" => $color,
        ));
    }
    
    /**
     * @Route("/temp", name="index_temp")
     */
    public function indexTemp()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        
        $listeProduits = $produitRepository->findByPrice(50);
        $produitDescription = '';
        
        foreach($listeProduits as $produitUnit){
                $produitDescription .= $produitUnit->getName() . " de catégorie " . $produitUnit->getCategory() . ";<br>";
        }
        
        return new Response($produitDescription);
    }


    
    
    
}
