<?php

namespace App\DataFixtures;

use App\Entity\Commune;
use App\Entity\Department;
use App\Entity\Profil;
use App\Entity\User;
use App\Repository\RegionRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $repo;
    private $encoder;
    public  function __construct(RegionRepository $repo, UserPasswordEncoderInterface $encoder)
    {
        $this->repo=$repo;
        $this->encoder= $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker= Factory::create('fr_FR');
        $regions= $this->repo->findAll();
        // Insertion des regions
        foreach ($regions as $region){
            $department = new Department();
            $department->setCode($faker->postcode)
                ->setNom($faker->city)
                ->setRegion($region);
            $manager->persist($department);

            //Pour chaque department on insere 10 communes
            for ($i=0;$i<10;$i++){
                $commune= new Commune();
                $commune->setCode($faker->postcode)
                    ->setNom($faker->city)
                    ->setDepartment($department);

                $manager->persist($commune);
            }
        }
        // $product = new Product();
        // $manager->persist($product);


        $profils=['Administrateur','Formateur','Apprenant','CM'];
        foreach ($profils as $key=>$libelle){
            $profil= new Profil();
            $profil->setLibelle($libelle);
            $manager->persist($profil);
            $manager->flush();
            for ($i=1;$i<=3;$i++){
                $user= new User();
                $user->setProfil($profil)
                    ->setNomComplet($faker->name)
                    ->setLogin(strtolower($libelle).$i);
                $hash= $this->encoder->encodePassword($user,'pass_1234');
                $user->setPassword($hash);
                $manager->persist($user);
            }

        }
        $manager->flush();
    }
}
