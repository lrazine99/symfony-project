<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        foreach(\App\Data\ListeProduit::$listeProduit as $dataProduit){
            $produit = new \App\Entity\Produit;

            $produit->setName($dataProduit['name']);
            $produit->setCategory($dataProduit['category']);
            $produit->setPrice($dataProduit['price']);
            $produit->setQuantity($dataProduit['quantity']);

            $manager->persist($produit);
        }

        $manager->flush();
    }
}