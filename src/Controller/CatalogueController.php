<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\CategoryRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'catalogue')]
    public function index(MenuRepository $repo, CategoryRepository $repoC, SessionInterface $session): Response
    {
        $categories = $repoC->findAll();
        $menus = [];
        $filtres = $session->get('filtres', []);
        if(count($filtres)>0){
            foreach ($filtres as $filtre) {
                $category = $repoC->findOneBy(['libelle'=>$filtre]);
                $menusCategory = $category->getMenus();
                foreach ($menusCategory as $mc) {
                    array_push($menus, $mc);    
                }
            }
        }   
        else{ 
            $menus = $repo->findAll();
        }
        return $this->render('catalogue/index.html.twig', [
            'controller_name' => 'CatalogueController',
            'menus' => $menus,
            'categories' => $categories,
            'filtres' => $filtres,
        ]);
    }

    #[Route('/catalogue/remove-filtre', name: 'remove_filtre')]
    public function removeFiltre(SessionInterface $session){  
        $filtres = $session->get('filtres', []);
        if(!empty($filtres)){
            $session->set('filtres', []);
        }
        return $this->redirectToRoute("catalogue");
    }
    
    #[Route('/catalogue/{filtre}', name: 'catalogue_filtre')]
    public function addFilter($filtre, SessionInterface $session)
    {
        $filtres = $session->get('filtres', []);
        if(in_array($filtre, $filtres)){
            unset($filtres[array_search($filtre, $filtres)]);
        }else{
            array_push($filtres, $filtre);
        }
        $session->set('filtres', $filtres);
        return $this->redirectToRoute("catalogue");
    }

    #[Route('/edit-product', name: 'edit_product')]
    public function dashboardProduct(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();
        return $this->render('catalogue/dashboard.produits.html.twig', [
            'controller_name' => 'HomeController',
            'produits' => $produits,
        ]);
    }

    #[Route('/delete-product/{id}', name: 'delete_product')]
    public function deleteProduct(Produit $produit, EntityManagerInterface $manager): Response{
        if($produit){
            $manager->remove($produit);
            $manager->flush();
            return $this->redirectToRoute('edit_product');
        }
    }
}
