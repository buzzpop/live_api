<?php

namespace App\Controller;

use App\Entity\Region;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/regions/api", name="api_add_region", methods={"get"})
     */
    public function addRegionByApi(SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $regionJson= file_get_contents("https://geo.api.gouv.fr/regions");
        /*
        // 1ere methode
        // Decode Json vers Tableau
        $regionTab= $serializer->decode($regionJson,'json');
        //Denormalisation Tableau vers Object ou Tableau d'Object
        $regionObject= $serializer->denormalize($regionTab,'App\Entity\Region[]');
        dd($regionObject);
         */
        //2e methode
        // Deserialisation Json vers Object
        $regionObject= $serializer->deserialize($regionJson,'App\Entity\Region[]','json');
        foreach ($regionObject as $region){
            $manager->persist($region);
        }
        $manager->flush();
        return  new JsonResponse('success', 200,[],'true');

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }
    /**
     * @Route("/api/regions", name="api_show_region_api", methods={"get"})
     */
    public function showRegion(SerializerInterface $serializer, RegionRepository $repo)
    {
        $regionObject=$repo->findAll();
        $regionObject= $serializer->serialize($regionObject,'json',[
            "groups"=>["region:read_all"]
        ]);
        return  new JsonResponse($regionObject, Response::HTTP_OK,[],'true');

    }

    /**
     * @Route("/api/regions", name="api_add_region_api", methods={"post"})
     */
    public function addRegion(SerializerInterface $serializer,Request $request, EntityManagerInterface $manager,ValidatorInterface $validator)
    {
        // Recuperer le contenu du Body de la requete
        $regionJson= $request->getContent();
        $region= $serializer->deserialize($regionJson,Region::class,'json');
        // Validation des donnees
        $errors= $validator->validate($region);
        if (count($errors)>0){
            $errorString= $serializer->serialize($errors,'json');
            return new JsonResponse($errorString,Response::HTTP_BAD_REQUEST,[],'true');
        }
        $manager->persist($region);
        $manager->flush();
        return new JsonResponse('success', Response::HTTP_CREATED,[],'true');


    }
}
