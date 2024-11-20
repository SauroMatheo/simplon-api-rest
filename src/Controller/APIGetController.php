<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Repository\EquipesRepository;
use App\Repository\JoueursRepository;

#[Route('/api/get')]
class APIGetController extends AbstractController
{
    #[Route('/', name: 'api_get')]
    public function index(JoueursRepository $joueursRepository, EquipesRepository $equipesRepository, SerializerInterface $serializer): JsonResponse
    {
        $equipes = $equipesRepository->findAll();
        $equipesJson = $serializer->serialize($equipes, 'json', ['groups' => 'equipesMinimum']);

        return new JsonResponse($equipesJson, Response::HTTP_OK, [], true);
    }

    #[Route('/equipe', name: 'api_get_equipe', methods: ['GET'])]
    public function equipe(EquipesRepository $equipesRepository, SerializerInterface $serializer): Response
    {
        if (isset($_GET["id"])) {
            $equipe = $equipesRepository->findById($_GET["id"]);
            $equipeJson = $serializer->serialize($equipe, 'json', ['groups' => 'equipe']);
    
            return new JsonResponse($equipeJson, Response::HTTP_OK, [], true);
        } else if (false) {
            
        } else {
            return new Response(Response::HTTP_BAD_REQUEST.': Bad Request', Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/joueur', name: 'api_get_joueur', methods: ['GET'])]
    public function joueur(JoueursRepository $joueursRepository, SerializerInterface $serializer): Response
    {
        if (isset($_GET["id"])) {
            $joueur = $joueursRepository->findById($_GET["id"]);
            $joueurJson = $serializer->serialize($joueur, 'json', ['groups' => 'joueur']);
    
            return new JsonResponse($joueurJson, Response::HTTP_OK, [], true);
        } else if (false) {
            
        } else {
            return new Response(Response::HTTP_BAD_REQUEST.': Bad Request', Response::HTTP_BAD_REQUEST);
        }
    }
}
