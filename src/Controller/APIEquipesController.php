<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\EquipesRepository;

use App\Entity\Equipes;

#[Route('/api/equipes')]
class APIEquipesController extends AbstractController
{
    #[Route('', name: 'api_get', methods: ['GET'])]
    public function getEquipeId(Request $request, EquipesRepository $equipesRepository, SerializerInterface $serializer): Response
    {
        (int) $id = $request->query->get('id');

        if (isset($id)) {
            $equipe = $equipesRepository->find($id);
            $equipeJson = $serializer->serialize($equipe, 'json', ['groups' => 'equipe']);

            return new JsonResponse($equipeJson, Response::HTTP_OK, [], true);
        } else {
            $equipes = $equipesRepository->findAll();
            $equipesJson = $serializer->serialize($equipes, 'json', ['groups' => 'equipesMinimum']);

            return new JsonResponse($equipesJson, Response::HTTP_OK, [], true);
        }
    }

    #[Route('', name: 'delete_equipe', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, EquipesRepository $equipesRepository): Response
    {
        (int) $id = $request->query->get('id');
        $equipe = $equipesRepository->find($id);

        $entityManager->remove($equipe);
        $entityManager->flush();

        if (null !== $equipesRepository->find($id)) {
            return new Response("", Response::HTTP_INTERNAL_SERVER_ERROR, [], true);
        }

        return new Response("", Response::HTTP_NO_CONTENT, [], true);
    }


    #[Route('', name: 'add_equipe', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        EquipesRepository $equipesRepository,
        SerializerInterface $serializer
        ): Response
    {
        $json = $request->getContent();
        $equipe = $serializer->deserialize($json, 'App\Entity\Equipes', "json");

        $entityManager->persist($equipe);
        $entityManager->flush();

        // return new Response(Response::HTTP_CREATED, Response::HTTP_CREATED, [], true);
        return new JsonResponse($equipe->getNom(), Response::HTTP_OK, [], true);
    }


    #[Route('', name: 'update_equipe', methods: ['PUT'])]
    public function modify(
        Request $request,
        EntityManagerInterface $em,
        EquipesRepository $repo,
        SerializerInterface $serializer
        ): JsonResponse
    {
        $json = json_decode($request->getContent(), true);

        
        $equipe = $repo->find($json['id']);

        
        if ($equipe === null) {
            return new JsonResponse(
                json([
                    "error" => "Entité introuvable"
                ]),
                Response::HTTP_NOT_FOUND,
                [],
                true
            );
        }

        $serializer->deserialize(
            $request->getContent(),
            'App\Entity\Equipes',
            "json",
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $equipe
            ]
        );

        $em->persist($equipe);
        $em->flush();

        $equipeJson = $serializer->serialize($equipe, 'json', ['groups' => 'equipe']);

        return new JsonResponse($equipeJson, Response::HTTP_OK, [], true);
    }
}
