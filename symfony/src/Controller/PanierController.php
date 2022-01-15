<?php

namespace App\Controller;
use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session\SessionInterface;


class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier')]
    public function index(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $panier = $session->get("panier", []);

        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;

        foreach($panier as $id => $quantite){
            $produit = $produitRepository->find($id);
            $dataPanier[] = [
                "produit" => $produit,
                "quantite" => $quantite
            ];
            $total += $produit->getPrix() * $quantite;
        }
        

        return $this->render('panier/index.html.twig', [
            'dataPanier'=>$dataPanier,
            'total'=>$total,
        ]);
    }
    #[Route('/add/{id}/{page}', name: 'add_panier')]
    public function add(Produit $produit,$page, SessionInterface $session){
       
        //on recuperer la panier
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);
        if($page){
            return $this->redirectToRoute('restaurant_show', ['id'=>$produit->getRestaurant()->getId()], Response::HTTP_SEE_OTHER);
        }
        else{
            return $this->redirectToRoute('panier');
        }
       
    }
    
    
    #[Route('/remove/{id}', name: 'remove_panier')]
    public function remove(Produit $produit, SessionInterface $session){
       
        //on recuperer le panier
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if(!empty($panier[$id])){
            if ($panier[$id]>1) {
                $panier[$id]--;
            }
            else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);
        return $this->redirectToRoute('panier');
    }
    #[Route('/delete/{id}', name: 'delete_panier')]
    public function delete(Produit $produit, SessionInterface $session){
       
        //on recuperer le panier
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);
        return $this->redirectToRoute('panier');
    }
   /* #[Route('/valide', name: 'valide_panier')]
    public function valide(){
        return $this->redirectToRoute('commande_new');
    }*/
}
