<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Repository\JoueursRepository;

#[Route('/api/joueurs')]
class APIJoueursController extends AbstractController
{
    #[Route('/get', name: 'get_joueurs', methods: ['GET'])]
    public function index(JoueursRepository $joueursRepository, SerializerInterface $serializer): JsonResponse
    {
        $joueurs = $joueursRepository->findAll();
        $joueursJson = $serializer->serialize($joueurs, 'json', ['groups' => 'joueursMinimum']);

        return new JsonResponse($joueursJson, Response::HTTP_OK, [], true);
    }

    #[Route('/get/{id}', name: 'get_joueur_id', methods: ['GET'])]
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
