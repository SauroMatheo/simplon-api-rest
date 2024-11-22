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
    public function getEquipes(
        Request $request,
        EquipesRepository $equipesRepository,
        SerializerInterface $serializer
        ): Response
    {
        (int) $id = $request->query->get('id');

        if (isset($id)) {
            $equipe = $equipesRepository->find($id);

            if ($equipe === null) {
                return $this->renvoiJson(Response::HTTP_NOT_FOUND, "Équipe introuvable");
            }

            $equipesJson = $serializer->serialize($equipe, 'json', ['groups' => 'equipe']);

        } else {
            $equipes = $equipesRepository->findAll();
            $equipesJson = $serializer->serialize($equipes, 'json', ['groups' => 'equipesMinimum']);
        }

        return new JsonResponse($equipesJson, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'delete_equipe', methods: ['DELETE'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        EquipesRepository $equipesRepository
        ): JsonResponse
    {
        if (!isset($id)) {
            return $this->renvoiJson(Response::HTTP_NOT_FOUND, "Équipe introuvable");
        }

        try {
            (int) $id = $request->query->get('id');
            $equipe = $equipesRepository->find($id);
        } catch (Exception $e) {
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        if ($equipe === null) {
            return $this->renvoiJson(Response::HTTP_NOT_FOUND, "Équipe introuvable");
        }

        $entityManager->remove($equipe);
        $entityManager->flush();

        if (null !== $equipesRepository->find($id)) {
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, "L'équipe n'a pas pu être supprimée");
        }

        return $this->renvoiJson(Response::HTTP_NO_CONTENT, "L'équipe a été supprimée avec succès");
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
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        $entityManager->persist($equipe);
        $entityManager->flush();


        return $this->renvoiJson(Response::HTTP_CREATED, "Équipe créée avec succès");
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

        if (!isset($json['id'])) {
            return $this->renvoiJson(Response::HTTP_BAD_REQUEST, "Aucun id spécifié");
        }

        try {
            $equipe = $repo->find($json['id']);
        } catch (Exception $e) {
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        if ($equipe === null) {
            return $this->renvoiJson(Response::HTTP_NOT_FOUND, "Équipe introuvable");
        }

        try {
            $serializer->deserialize(
                $request->getContent(),
                'App\Entity\Equipes',
                "json",
                [AbstractNormalizer::OBJECT_TO_POPULATE => $equipe]
            );
        } catch (Exception $e) {
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        $em->persist($equipe);
        $em->flush();

        $equipeJson = $serializer->serialize($equipe, 'json', ['groups' => 'equipe']);


        return new JsonResponse($equipeJson, Response::HTTP_OK, [], true);
    }


    // Pour rendre le code plus propre
    private function renvoiJson(int $statut, ?string $details): JsonResponse
    {
        return new JsonResponse(
            json_encode([
                "statut" => $statut,
                "details" => $details
            ]),
            $statut,
            [],
            true
        );
    } 
}
