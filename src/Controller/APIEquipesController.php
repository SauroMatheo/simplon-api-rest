<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Repository\EquipesRepository;

#[Route('/api/equipes')]
class APIEquipesController extends AbstractController
{
    #[Route('/get', name: 'api_get')]
    public function getEquipes(EquipesRepository $equipesRepository, SerializerInterface $serializer): JsonResponse
    {
        $equipes = $equipesRepository->findAll();
        $equipesJson = $serializer->serialize($equipes, 'json', ['groups' => 'equipesMinimum']);

        return new JsonResponse($equipesJson, Response::HTTP_OK, [], true);
    }

    #[Route('/get/{id}', name: 'api_get_equipe', methods: ['GET'])]
    public function getEquipeId(EquipesRepository $equipesRepository, SerializerInterface $serializer, int $id): Response
    {
        $equipe = $equipesRepository->findById($id);
        $equipeJson = $serializer->serialize($equipe, 'json', ['groups' => 'equipe']);

        return new JsonResponse($equipeJson, Response::HTTP_OK, [], true);
    }
}
