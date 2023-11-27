<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\Commande1Type;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande/crud')]
class CommandeCrudController extends AbstractController
{
    #[Route('/', name: 'app_commande_crud_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande_crud/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/mesCommande/{id}', name: 'app_commande_mon_index', methods: ['GET'])]
    public function monIndex(CommandeRepository $commandeRepository, $id): Response
    {
    //     $x = $commandeRepository->findBy(['utilisateur' => $this->getUser()]);
    //     $y = $commandeRepository->myCommande();
    //     dd($y);
    //    // dd($x[2]->getId());
        return $this->render('commande_crud/mescommande.html.twig', [
            'commandes' => $commandeRepository->myCommande($id)
        ]);
    }
    #[Route('/facture/{id}', name: 'app_commande_mon_index', methods: ['GET'])]
    public function facture(CommandeRepository $commandeRepository, $id): Response
    {
    //     $x = $commandeRepository->findBy(['utilisateur' => $this->getUser()]);
    //     $y = $commandeRepository->myCommande();
    //     dd($y);
    //    // dd($x[2]->getId());
        return $this->render('commande_crud/facture.html.twig', [
            'commandes' => $commandeRepository->myCommande($id)
        ]);
    }

    #[Route('/new', name: 'app_commande_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(Commande1Type::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_crud/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_crud_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande_crud/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Commande1Type::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_crud/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
