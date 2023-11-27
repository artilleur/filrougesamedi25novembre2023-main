<?php

// src/Controller/FacturationController.php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacturationController extends AbstractController
{
    
    #[ Route("/facturation/facture/{id}", name:"app_facturation_facture")]
     
    public function facture($id, CommandeRepository $commandeRepository): Response
    {
        // Récupérer les données nécessaires depuis la base de données
        // $entityManager = $this->getDoctrine()->getManager();
        // $commandeRepository = $entityManager->getRepository(Commande::class); // Remplacez 'Commande' par le nom de votre entité
        $comId = $commandeRepository->findOneBy(['id' => $id])->getId();
        $utiId = $commandeRepository->findOneBy(['id' => $id])->getUtilisateur();
        $facId = $comId;
        $comDate = $commandeRepository->findOneBy(['id' => $id])->getDateCommande();
        $facDate = $commandeRepository->findOneBy(['id' => $id])->getDateCommande();
         $commandes = $commandeRepository->myCommande($id);
// dd($commandes);
if ($utiId->getRoles()[0]=='ROLE_COMMERCE') {
    $tva =20;
    $remise =5;
} 
 elseif ($utiId->getRoles()[0]=='ROLE_ADMIN') {
    $tva =20;
    $remise=10;
    
} 
else {
    $tva = 20;
    $remise=0;
}
// if ($this->isGranted('ROLE_COMMERCE')) {
//     $remise =5;
// } 

        // Rendre le template avec les données
        return $this->render('facturation/facture.html.twig', [
            'remise'=>$remise,
            'tva' => $tva,
            'comId' => $comId,
            'utiId' => $utiId->getId(),
            'nom' => $commandeRepository->myCommandeByCom($id)[0]['nom'],
            'prenom' => $commandeRepository->myCommandeByCom($id)[0]['prenom'],
            'adresse' => $commandeRepository->myCommandeByCom($id)[0]['c_adLiv'],
            'adresseFac' => $commandeRepository->myCommandeByCom($id)[0]['c_adFac'],
            'tel' => $commandeRepository->myCommandeByCom($id)[0]['user_tel'],
            'email' => $commandeRepository->myCommandeByCom($id)[0]['user_email'],
            'adresse' => $commandeRepository->myCommandeByCom($id)[0]['c_adLiv'],
            'facId' => $facId,
            'comDate' => $comDate,
            'facDate' => $facDate,
            'commandes' => $commandeRepository->myCommandeByCom($id),
            // 'totals' => $commandeRepository->totalPrixCom($id),
            // 'commandes' => $commandes,
        ]);
    
        
    }
    #[ Route("/facturation/index/{id}", name:"app_facturation_index")]
     
    public function bon($id, CommandeRepository $commandeRepository): Response
    {
        // Récupérer les données nécessaires depuis la base de données
        // $entityManager = $this->getDoctrine()->getManager();
        // $commandeRepository = $entityManager->getRepository(Commande::class); // Remplacez 'Commande' par le nom de votre entité
        $comId = $commandeRepository->findOneBy(['id' => $id])->getId();
        $utiId = $commandeRepository->findOneBy(['id' => $id])->getUtilisateur()->getId();
        $facId = $comId;
        $comDate = $commandeRepository->findOneBy(['id' => $id])->getDateCommande();
        $facDate = $commandeRepository->findOneBy(['id' => $id])->getDateCommande();
         $commandes = $commandeRepository->myCommande($id);
// dd($commandes);
        // Rendre le template avec les données
        return $this->render('facturation/index.html.twig', [
            'comId' => $comId,
            'utiId' => $utiId,
            'nom' => $commandeRepository->myCommandeByCom($id)[0]['nom'],
            'prenom' => $commandeRepository->myCommandeByCom($id)[0]['prenom'],
            'adresse' => $commandeRepository->myCommandeByCom($id)[0]['c_adLiv'],
            'adresseFac' => $commandeRepository->myCommandeByCom($id)[0]['c_adFac'],
            'tel' => $commandeRepository->myCommandeByCom($id)[0]['user_tel'],
            'email' => $commandeRepository->myCommandeByCom($id)[0]['user_email'],
            'adresse' => $commandeRepository->myCommandeByCom($id)[0]['c_adLiv'],
            'facId' => $facId,
            'comDate' => $comDate,
            'facDate' => $facDate,
            'commandes' => $commandeRepository->myCommandeByCom($id),
            // 'totals' => $commandeRepository->totalPrixCom($id),
            // 'commandes' => $commandes,
        ]);
        
    }
}


