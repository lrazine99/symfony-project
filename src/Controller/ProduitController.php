<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Category;
use App\Entity\Commande;
use App\Form\ProduitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    

    /**
     * @Route("/fiche/{idProduit}", name="produit_fiche")
     */
    public function produitFiche(Request $request, $idProduit): Response
    {
        // On récupère l'Entity Manager et le Repository de Produit
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $commandeRepository = $entityManager->getRepository(Commande::class);
        
        // Nous récupérons le Produit mentionné dans notre URL
        $selectedProduit = $produitRepository->find($idProduit);
        
        //Création d'un formulaire d'achat, une quantité + un bouton
        $form = $this->createFormBuilder()
                ->add('quantity', IntegerType::class, [
                    'label' => 'Quantité',
                    ])
                ->add('achat', SubmitType::class,               [
                    'label' => 'Acheter',
                    'attr' => [
                        'style' => 'margin-top : 5px',
                        'class' => 'w3-button w3-black w3-margin-bottom',
                    ],
                ])
                ->getForm()
                ;
        // Prise en compte du formulaire émis, et vérification du type de méthode (POST), de la validité du formulaire et d'une connexion utilisateur, getUser rend null si aucun connexion, invalidant la structure if()
        $form->handleRequest($request);
        if($request->isMethod('post') && $form->isValid() && $this->getUser()){
            // getData permet de récupérer les informations du formulaire afin de pouvoir les entrer dans un nouvel objet (que nous allons créer)
            $data = $form->getData();
            
            // Si le produit visé a une quantité supérieure à 0, nous créons notre Réservation et nous décrémentons la Quantity
            if($selectedProduit->getQuantity() > 0){
                $newProduitQuantity = $selectedProduit->getQuantity() - $data['quantity'];
                
                // Si la quantité requise est supérieure à la quantité du Produit, cette dernière est automatiquement mise à zéro, tandis que la Reservation à venir récupère automatiquement le reste de la quantité possédée par le Produit
                if($newProduitQuantity < 0){
                    $reservationQuantity = $selectedProduit->getQuantity();
                    $selectedProduit->setQuantity(0);
                } else {
                    $reservationQuantity = $data['quantity'];
                    $selectedProduit->setQuantity($newProduitQuantity);
                }

                $activeCommande = $commandeRepository->findByStatut('panier');
                if(empty($activeCommande)){
                    $activeCommande = new \App\Entity\Commande;
                }else{
                    $activeCommande = end($activeCommande);
                }

                $reservation = new \App\Entity\Reservation;
                $reservation->setProduit($selectedProduit);
                $reservation->setQuantity($reservationQuantity);

                $activeCommande->addReservation($reservation);

                $entityManager->persist($activeCommande);
                $entityManager->persist($reservation);
                $entityManager->persist($selectedProduit);
            }
        }
        
            $entityManager->flush();
        // Rendu de la fiche Produit
        return $this->render('produit/ficheproduit.html.twig', array(
            'form' => $form->createView(),
            'selectedProduit' => $selectedProduit,
        ));
    }
    
     /**
     * @Route("/delete-reservation/{idReservation}", name="reservation_delete")
     */
    public function deleteReservation(Request $request, $idReservation){
        // Nous récupérons l'Entity Manager et le Repository de Reservation
	$entityManager = $this->getDoctrine()->getManager();
	$reservationRepository = $entityManager->getRepository(\App\Entity\Reservation::class);
        
        // Nous récupérons la Réservation indiquée dans notre URL
	$reservation = $reservationRepository->find($idReservation);
        
        // Si la réservation existe, nous récupérons le produit associé et nous lui ajoutons la quantité de la Réservation. Nous persistons le Produit et nous supprimons la Reservation
        if($reservation){
            $commande = $reservation->getCommande();
            $commande->removeReservation($reservation);
            
            $produit = $reservation->getProduit();
            $produit->setQuantity($produit->getQuantity() + $reservation->getQuantity());
            
            // Nous vérifions ici si la Commande est vide après la suppression de la Reservation. isEmpty() est une fonction spéciale de ArrayCollection qui permet de vérifier si notre collection d'Entity est vide
            if($commande->getReservations()->isEmpty()){
                $entityManager->remove($commande);
            } else {
                $entityManager->persist($commande);
            }
            
            $entityManager->persist($produit);
            $entityManager->remove($reservation);
            $entityManager->flush();
        }
        
        return $this->redirect($this->generateUrl('admin_options'));
    }
    
     /**
     * @Route("/delete-commande/{idCommande}", name="commande_delete")
     */
    public function deleteCommande(Request $request, $idCommande){
        // Nous récupérons l'Entity Manager et le Repository de Commande
	$entityManager = $this->getDoctrine()->getManager();
	$commandeRepository = $entityManager->getRepository(\App\Entity\Commande::class);
        
        // Nous récupérons la Commande indiquée dans notre URL
	$selectedCommande = $commandeRepository->find($idCommande);
        $listeReservations = $selectedCommande->getReservations();
        
        // Nous récupérons la liste des Reservation que nous supprimons une par une tout en rendant leur quantité à celle du Produit concerné
        foreach($listeReservations as $reservation){
            $produit = $reservation->getProduit();
            $produit->setQuantity($produit->getQuantity() + $reservation->getQuantity());
            
            $selectedCommande->removeReservation($reservation);
            
            $entityManager->persist($produit);
            $entityManager->remove($reservation);
        }
        
        $entityManager->remove($selectedCommande);
        $entityManager->flush();
        
        return $this->redirect($this->generateUrl('admin_options'));
    }
    
     /**
     * @Route("/categorie/{idCategory}", name="categorie")
     */
    public function category(Request $request, $idCategory): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $listeProduit = $produitRepository->findByCategory($idCategory);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $listeCategory = $categoryRepository->findAll();
        
        
        
        return $this->render('index/index.html.twig', [
            'listeProduit' => $listeProduit,
            'listeCategory' => $listeCategory,
            ]);
    }
        
        
       
}
