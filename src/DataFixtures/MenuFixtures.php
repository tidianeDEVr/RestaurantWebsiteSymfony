<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use ArrayObject;
use App\Entity\Category;
use App\Entity\Produit;
use App\Entity\Menu;

class MenuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $libelles=["burger","boisson", "frite", "complet"];
        $categories=[
            ["pain", "viande", "oignon", "salade", "cheddar"], 	
            ["eau","caramel","acide", "sucre"],
            ["pomme de terre", "sel"]
        ];
        $mns = ["menu_burger", "menu_complet", "menu_boisson"];
        $burgers = new ArrayObject();
        $boissons = new ArrayObject();
        $frites = new ArrayObject();
        $categoriesObj = new ArrayObject();

        foreach ($libelles as $libelle) {
            $cat=new Category;
            $cat->setLibelle($libelle)
                  ->setDescription("Description ".$libelle);
            $categoriesObj->append($cat);
            $manager->persist($cat);
        
        
            for ($i=1; $i < 6; $i++) { 
                $produit=new Produit;
                $produit->setLibelle(ucfirst($cat->getLibelle()).$i);
                if($cat->getLibelle()=="burger"){
                    $produit->setPrix(1500);
                    $produit->setComposants($categories[0]); 
                    $burgers->append($produit);  
                }elseif($cat->getLibelle()=="boisson"){
                    $produit->setPrix(500);
                    $produit->setComposants($categories[1]);
                    $boissons->append($produit);   
                }else{
                    $produit->setPrix(800);
                    $produit->setComposants($categories[2]);   
                    $frites->append($produit);
                }
                $manager->persist($produit);
            }
        }

        foreach ($mns as $m) {
            for ($i=1; $i < 6; $i++) { 
                $menu=new Menu;
                $menu->setLibelle(str_replace('_', " ", $m).' '.$i);
                $menu->setDescription('Desciption '.str_replace('_', ' ', $m));
                if($m=="menu_burger"){
                    $menu->addProduit($burgers[rand(0,4)]); 
                    $menu->setCategory($categoriesObj[0]);
                }
                elseif($m=="menu_complet"){
                    $menu->addProduit($burgers[rand(0,4)]);
                    $menu->addProduit($frites[rand(0,4)]);
                    $menu->addProduit($boissons[rand(0,4)]);
                    $menu->setCategory($categoriesObj[3]);
                }
                else{
                    $menu->addProduit($boissons[rand(0,4)]);
                    $menu->setCategory($categoriesObj[1]);
                }
                $menu->setImage($m.$i.".jpg");
                $manager->persist($menu);
            }
        }

        $manager->flush();
    }
}
