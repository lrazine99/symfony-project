<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit", inversedBy="reservations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $produit;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commande", inversedBy="reservations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $commande;

    public function getId(): ?int
    {
        return $this->id;
    }

    

    

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}
