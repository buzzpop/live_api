<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApprenantController extends AbstractController
{
    /**
     * @Route("/api/apprenants", name="apprenant_liste",
     *     methods={"get"},
     * defaults={
     *      "_controller"= "App\Controller\ApprenantController::showApprenant",
     *      "_api_resource_class"=" App\Entity\User",
     *      "_api_item_operation_name": "get_apprenants"
     *     }
     * )
     */
    public function showApprenant(UserRepository $repo)
    {
        $apprenants= $repo->findByProfil("Apprenant");
       return $this->json($apprenants,Response::HTTP_OK,[]);
    }
}
