<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Client;
use App\Entity\Gestionnaire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder){
        $this->encoder=$encoder;
    }
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i < 11; $i++) { 
            $data=new Client;
            $data->setNomComplet('client'.$i);
            $data->setDomicile('adresse de residence')    
            ->setTelephone('221 78 123 45 67')
            ->setLogin(strtolower("client").$i."@gmail.com");
            $plainPassword="passer@123";
           $passwordEncode= $this->encoder->hashPassword($data,$plainPassword);
           $data->setPassword($passwordEncode);
           $this->addReference("Client".$i, $data);
           $manager->persist($data);  
        }

        for ($i=1; $i < 11; $i++) { 
            $data=new Gestionnaire;
            $data->setNomComplet('gestionnaire'.$i); 
            $data->setLogin(strtolower("gestionnaire").$i."@gmail.com")
            ->setTelephone('221 78 123 45 67');
            $data->setNci(strval(uniqid()));
            $plainPassword="passer@123";
           $passwordEncode= $this->encoder->hashPassword($data,$plainPassword);
           $data->setPassword($passwordEncode);
           $this->addReference("Gestionnaire".$i, $data);
           $manager->persist($data);  
        }

        $manager->flush();
    }
}
