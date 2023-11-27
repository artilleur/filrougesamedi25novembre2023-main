<?php

namespace App\Entity;

use App\Entity\Commande;
use App\Entity\Utilisateur;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FactureRepository;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    //  #[ORM\Column]
    //  private ?int $fact_id = null;

     #[ORM\Column(length: 255)]
     private ?string $cli_nom = null;

     #[ORM\Column(length: 255)]
     private ?string $cli_prenom = null;

     #[ORM\Column(length: 255)]
     private ?string $cli_email = null;

     #[ORM\Column(length: 255)]
     private ?string $cli_telephone = null;

     #[ORM\Column(length: 255)]
     private ?string $adresse_livraison = null;

     #[ORM\Column(length: 255)]
     private ?string $adresse_facturation = null;

     #[ORM\Column]
     private ?int $id_commande = null;

     #[ORM\Column(length: 255)]
     private ?string $produit = null;

     #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
     private ?string $prix = null;

     #[ORM\Column]
     private ?int $quantite = null;

     #[ORM\ManyToOne(inversedBy: 'Utilisateur')]
     private ?utilisateur $user = null;

     #[ORM\ManyToOne(inversedBy: 'Commande')]
     private ?Commande $com_id = null;

     #[ORM\ManyToOne(inversedBy: 'factures')]
     private ?CommandeDetail $com_detail = null;
     

  
     public function getId(): ?int
     {
         return $this->id;
     }

     public function getCliNom(): ?string
     {
         return $this->cli_nom;
     }

     public function setCliNom(string $cli_nom): static
     {
         $this->cli_nom = $cli_nom;

         return $this;
     }

     public function getCliPrenom(): ?string
     {
         return $this->cli_prenom;
     }

     public function setCliPrenom(string $cli_prenom): static
     {
         $this->cli_prenom = $cli_prenom;

         return $this;
     }

     public function getCliEmail(): ?string
     {
         return $this->cli_email;
     }

     public function setCliEmail(string $cli_email): static
     {
         $this->cli_email = $cli_email;

         return $this;
     }

     public function getCliTelephone(): ?string
     {
         return $this->cli_telephone;
     }

     public function setCliTelephone(string $cli_telephone): static
     {
         $this->cli_telephone = $cli_telephone;

         return $this;
     }

     public function getAdresseLivraison(): ?string
     {
         return $this->adresse_livraison;
     }

     public function setAdresseLivraison(string $adresse_livraison): static
     {
         $this->adresse_livraison = $adresse_livraison;

         return $this;
     }

     public function getAdresseFacturation(): ?string
     {
         return $this->adresse_facturation;
     }

     public function setAdresseFacturation(string $adresse_facturation): static
     {
         $this->adresse_facturation = $adresse_facturation;

         return $this;
     }

     public function getIdCommande(): ?int
     {
         return $this->id_commande;
     }

     public function setIdCommande(int $id_commande): static
     {
         $this->id_commande = $id_commande;

         return $this;
     }
     
    

     public function getProduit(): ?string
     {
         return $this->produit;
     }
     

   
    //  public function __toString() {
    //     return $this->produit;
    // }
    public function setProduit(string $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

     public function getPrix(): ?string
     {
         return $this->prix;
     }

     public function setPrix(string $prix): static
     {
         $this->prix = $prix;

         return $this;
     }

     public function getQuantite(): ?int
     {
         return $this->quantite;
     }

     public function setQuantite(int $quantite): static
     {
         $this->quantite = $quantite;

         return $this;
     }

     public function getUser(): ?utilisateur
     {
         return $this->user;
     }

     public function setUser(?utilisateur $user): static
     {
         $this->user = $user;

         return $this;
     }

     public function getComId(): ?Commande
     {
         return $this->com_id;
     }

     public function setComId(?Commande $com_id): static
     {
         $this->com_id = $com_id;

         return $this;
     }

     public function getComDetail(): ?CommandeDetail
     {
         return $this->com_detail;
     }

     public function setComDetail(?CommandeDetail $com_detail): static
     {
         $this->com_detail = $com_detail;

         return $this;
     }

    
     
}

    