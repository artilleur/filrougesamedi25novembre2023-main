<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\OrderType;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Entity\CommandeDetail;
use App\Form\Utilisateur1Type;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders', name: 'app_commande_')]

class CommandeController extends AbstractController
{     
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProduitRepository $productsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $panier = $session->get('panier', []);
        if($panier=== []){
            $this->addFlash('message', 'votre panier est vide');
           return  $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(OrderType::class,data: null,options: [
            "user" => $this->getUser()
        ]);
       // $commande = new Commande();
        // $form=$this->createForm(CommandeType::class,data: null);
        // dd($this->getUser()->getNom());
        $userData=$this->getUser();
        
        $data = [];
        $total= 0;
        $soustotal = 0;
        $fdp = 6;
        $totaltva = 0;
       

if ($this->isGranted('ROLE_ADMIN')) {

    $tva = 20; 
    $remise= 10;
} elseif ($this->isGranted('ROLE_COMMERCIAL')) {
    
    $tva = 20; 

} elseif ($this->isGranted('ROLE_COMMERCE')) {
    
    $tva = 20; 
    $remise= 5;

}

elseif ($this->isGranted('ROLE_USER')) {
    
    $tva = 20; 
    $remise =0;

} else  {
    
    $tva = 20;
    $remise = 0;
}

        foreach($panier as $id => $quantity){
            $product = $productsRepository->find($id);
            

            $data[] = [
                'product' => $product,
                'quantity' => $quantity,
                'remise' => $remise,
            ];
            //dd($data);
            $soustotal += $product->getPrix() * $quantity;
            
            }

            $totaltva += round($soustotal+($soustotal*$tva/100)-($soustotal*$remise/100),2);



           

            if($totaltva>100) {
                $total =$totaltva+0; 
            }
            else {
            $total =$totaltva+$fdp ;
            
        }

        
        // return $this->render('commande/index.html.twig', compact('data', 'soustotal', 'total','userData', 'fdp','form'));
        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),  // Convert Form to FormView
           'data' => $data,
            'soustotal' => $soustotal,
            'totaltva' => $totaltva,
            'total' => $total,
            'userData' => $userData,
            'fdp' => $fdp,
            'tva'=> $tva,
            'remise'=> $remise,
        ]);
        

    }


    #[Route('/ajout', name: 'add', methods: ['GET', 'POST'])]

    public function add(SessionInterface $session, ProduitRepository $produitRepository, EntityManagerInterface $em, Request $request): Response
    {
       $this->denyAccessUnlessGranted('ROLE_USER');

       $panier=$session->get('panier', []);
       $data = [];
       $total= 0;
       $soustotal = 0;
       $fdp = 6;
       $totaltva = 0;
    //    $remise = 0;

if ($this->isGranted('ROLE_ADMIN')) {

   $tva = 20;
   $remise= 10;
} elseif ($this->isGranted('ROLE_COMMERCIAL')) {
   
   $tva = 20; 
   $remise=0;
} elseif ($this->isGranted('ROLE_COMMERCE')) {
   
   $tva = 20; 
   $remise=5;
}

elseif ($this->isGranted('ROLE_USER')) {
   
   $tva = 20; 
   $remise=0;
} else  {
   
   $tva = 20;
   $remise=0;
}
// if ($this->isGranted('ROLE_COMMERCE')) {
   
//     $remise = 5; 
 
//  } else  {
    
//     $remise = 0;
//  }

       foreach($panier as $id => $quantity){
           $product = $produitRepository->find($id);
           

           $data[] = [
               'product' => $product,
               'quantity' => $quantity,
               
           ];
           //dd($data);
           $soustotal += $product->getPrix() * $quantity;
           
           }

           $totaltva +=round( $soustotal+($soustotal*$tva/100)-($soustotal*$remise/100),2);



          

           if($totaltva>100) {
               $total =$totaltva+0; 
           }
           else {
           $total =$totaltva+$fdp ;
           
       }

       if($panier=== []){
        $this->addFlash('message', 'votre panier est vide');
       return  $this->redirectToRoute('app_home');
       }
      
       $form = $this->createForm(OrderType::class,data: null,options: [
        "user" => $this->getUser()
    ]);
    $form->handleRequest($request);
    
    //dd( $form->get('transporteur')->getData());
    if ($form->isSubmitted() && $form->isValid()) {
        $adresseLivraison = $form->get('adresse')->getData();
    
        $total= $total;
        $commentaire = $form->get('commentaire')->getData();
        $adresseFacture = $form->get('adresse_fact')->getData();
       
    



       //le panier n'est pas vide, on cree la commande

       $commande = new Commande();
       //on remplit la commande
       $commande->setUtilisateur($this->getUser());
       $commande->setAdresseFact(str_replace("[-br]", " ",$adresseFacture));
       
        $commande->setAdresse(str_replace("[-br]", " ",$adresseLivraison));
        
        $commande->setCommentaire($commentaire);
       
       //on parcourt le panier pour ceer les details de commande
       foreach($panier as $item => $quantity){
        $commandeDetails = new CommandeDetail();
        //on va chercher le produit
        $produit = $produitRepository->find($item);
        $prix = $produit->getPrix();
        //on cree le details de commmande
        $commandeDetails->setPro($produit);
        $commandeDetails->setPrix($prix);
        $commandeDetails->setQuantite($quantity);

        $commande->addCommandeDetail($commandeDetails);

        

       }
       //on persiste et on flush
       $em->persist($commande);
       $em->flush();
       $id= $commande->getId();
    }

           $session->remove('panier');
            
            
        // return $this->render('commande/index.html.twig', [
        //     'controller_name' => 'CommandeController',
      //  ]);
      return $this->render('commande/recap.html.twig',compact('total','fdp','remise','soustotal','id','data','tva','totaltva'));

    }

    #[Route('/{id}/edit', name: 'app_adresse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Adresse $adresse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_adresse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('adresse/edit.html.twig', [
            'adresse' => $adresse,
            'form' => $form,
        ]);
    }
}
