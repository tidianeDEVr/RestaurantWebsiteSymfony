<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandeRepository;
use App\Repository\ClientRepository;
use App\Repository\MenuRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(CommandeRepository $repo, ClientRepository $repoClient, MenuRepository $repoM): Response
    {
        
        $commandes = $repo->findAll();
        $clients = $repoClient->findAll();
        $menus = $repoM->findAll();
        
        $revenu=0;
        $nombreClient=0;
        $commandeValide=0;
        $commandeInvalide=0;
        $commandeEnCours=0;
        $menu = $menus[0];
        
        foreach ($commandes as $commande) {
            if($commande->getEtat()=='Prete'){
                $commandeValide++;
                $revenu += $commande->getPrix();
            }elseif($commande->getEtat()=='Annulee'){
                $commandeInvalide++;
            }else{
                $commandeEnCours++;
            }
        }
        foreach ($clients as $client) {
            $nombreClient++;
        }
        foreach ($menus as $mn){
            if(count($menu->getCommandes())<count($mn->getCommandes())){
                $menu = $mn;
            }
        }
        return $this->render('home/dashboard.html.twig', [
            'controller_name' => 'GestionnaireController',
            'commandes' => $commandes,
            'nombreClients' => $nombreClient,
            'revenu' => $revenu,
            'menuPlusVendu' => $menu,
            'commandeValide' => $commandeValide,
            'commandeInvalide' => $commandeInvalide,
            'commandeEnCours'=> $commandeEnCours,
        ]);
    }
}
