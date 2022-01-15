<?php

namespace App\Controller;

use App\Entity\LigneDeCommande;
use App\Entity\Commande;
use App\Form\LigneDeCommandeType;
use App\Repository\LigneDeCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProduitRepository;

#[Route('/ligne/de/commande')]
class LigneDeCommandeController extends AbstractController
{
    #[Route('/', name: 'ligne_de_commande_index', methods: ['GET'])]
    public function index(LigneDeCommandeRepository $ligneDeCommandeRepository): Response
    {
        return $this->render('ligne_de_commande/index.html.twig', [
            'ligne_de_commandes' => $ligneDeCommandeRepository->findAll(),
        ]);
    }

    #[Route('/{id}/new', name: 'ligne_de_commande_new')]
    public function new(SessionInterface $session,Commande $commande, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {

            $panier = $session->get("panier", []);
            foreach($panier as $id => $quantite){
                //Créer une cligne de commande pour chaque produit su panier
                $ligneDeCommande = new LigneDeCommande();
                $produit = $produitRepository->find($id);
                $ligneDeCommande->setCommande($commande);
                $ligneDeCommande->setProduit($produit);
                $ligneDeCommande->setQuantite($quantite);
                $entityManager->persist($ligneDeCommande);
                $entityManager->flush();
            }
            //Vide le panier
            $session->remove("panier");
            return $this->redirectToRoute('user_show', ['id'=>$this->getUser()->getId()], Response::HTTP_SEE_OTHER);
   

       
    }

    #[Route('/{id}', name: 'ligne_de_commande_show', methods: ['GET'])]
    public function show(LigneDeCommande $ligneDeCommande): Response
    {
        return $this->render('ligne_de_commande/show.html.twig', [
            'ligne_de_commande' => $ligneDeCommande,
        ]);
    }

    #[Route('/{id}/edit', name: 'ligne_de_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LigneDeCommande $ligneDeCommande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LigneDeCommandeType::class, $ligneDeCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('ligne_de_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ligne_de_commande/edit.html.twig', [
            'ligne_de_commande' => $ligneDeCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'ligne_de_commande_delete', methods: ['POST'])]
    public function delete(Request $request, LigneDeCommande $ligneDeCommande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ligneDeCommande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ligneDeCommande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ligne_de_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
