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


#[Route('/api/equipes')]
class APIEquipesController extends AbstractController
{
    #[Route('', name: 'get_equipes', methods: ['GET'])]
    public function getEquipes(Request $request, EquipesRepository $equipesRepository, SerializerInterface $serializer): Response
    {
        (int) $id = $request->query->get('id');

        if (isset($id)) {
            $equipe = $equipesRepository->find($id);

            if ($equipe === null) { return
                new JsonResponse(
                    json_encode(["status" => "Not Found", "details" => "Entité introuvable"]),
                    Response::HTTP_NOT_FOUND,
                    [], true
                );
            }

            $equipesJson = $serializer->serialize($equipe, 'json', ['groups' => 'equipe']);

        } else {
            $equipes = $equipesRepository->findAll();
            $equipesJson = $serializer->serialize($equipes, 'json', ['groups' => 'equipesMinimum']);
        }

        return new JsonResponse($equipesJson, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'delete_equipe', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, EquipesRepository $equipesRepository): JsonResponse
    {
        if (!isset($id)) { return
            new JsonResponse(
                json_encode(["status" => "Not Found", "details" => "Entité introuvable"]),
                Response::HTTP_NOT_FOUND,
                [], true
            );
        }

        try {
            (int) $id = $request->query->get('id');
            $equipe = $equipesRepository->find($id);
        } catch (Exception $e) {
            return new JsonResponse(
                json([
                    "status" => "Internal Server Error",
                    "details" => $e
                ]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                [], true
            );
        }

        if ($equipe === null) { return
            new JsonResponse(
                json_encode(["status" => "Not Found", "details" => "Entité introuvable"]),
                Response::HTTP_NOT_FOUND,
                [], true
            );
        }

        $entityManager->remove($equipe);
        $entityManager->flush();

        if (null !== $equipesRepository->find($id)) {
            return new JsonResponse(
                json([
                    "status" => "Internal Server Error",
                    "details" => "L'entité n'a pas pu être supprimée"
                ]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                [], true);
        }

        return new JsonResponse(
            json([
                "status" => "No Content",
                "details" => "L'entité a été supprimée"
            ]),
            Response::HTTP_NO_CONTENT,
            [], true);
    }


    #[Route('', name: 'add_equipe', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
        ): JsonResponse
    {
        try {
            $json = $request->getContent();
            $equipe = $serializer->deserialize($json, 'App\Entity\Equipes', "json");
        } catch (Exception $e) {
            return new JsonResponse(
                json([
                    "status" => "Internal Server Error",
                    "details" => $e
                ]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                [], true
            );
        }

        $entityManager->persist($equipe);
        $entityManager->flush();


        return new JsonResponse(
            json([
                "status" => "OK",
                "details" => "Équipe créée avec succès"
            ]),
            Response::HTTP_OK,
            [], true
        );
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

        if (!isset($json['id'])) { return
            new JsonResponse(
                json_encode(["status" => "Bad Request", "details" => "Aucun id spécifié"]),
                Response::HTTP_BAD_REQUEST,
                [], true
            );
        }

        try {
            $equipe = $repo->find($json['id']);
        } catch (Exception $e) {
            return new JsonResponse(
                json([
                    "status" => "Internal Server Error",
                    "details" => $e
                ]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                [], true
            );
        }

        if ($equipe === null) { return
            new JsonResponse(
                json_encode(["status" => "Not Found", "details" => "Entité introuvable"]),
                Response::HTTP_NOT_FOUND,
                [], true
            );
        }

        try {
            $serializer->deserialize(
                $request->getContent(),
                'App\Entity\Equipes',
                "json",
                [AbstractNormalizer::OBJECT_TO_POPULATE => $equipe]
            );
        } catch (Exception $e) {
            return new JsonResponse(
                json([
                    "status" => "Internal Server Error",
                    "details" => $e
                ]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                [], true
            );
        }

        $em->persist($equipe);
        $em->flush();

        $equipeJson = $serializer->serialize($equipe, 'json', ['groups' => 'equipe']);


        return new JsonResponse($equipeJson, Response::HTTP_OK, [], true);
    }
}
