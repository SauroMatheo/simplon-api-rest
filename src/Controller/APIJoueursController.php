<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\JoueursRepository;

#[Route('/api/joueurs')]
class APIJoueursController extends AbstractController
{
    #[Route('/get', name: 'get_joueurs', methods: ['GET'])]
    public function getJoueur(JoueursRepository $joueursRepository, SerializerInterface $serializer): JsonResponse
    {
        $joueurs = $joueursRepository->findAll();
        $joueursJson = $serializer->serialize($joueurs, 'json', ['groups' => 'joueursMinimum']);

        return new JsonResponse($joueursJson, Response::HTTP_OK, [], true);
    }

    #[Route('/get/{id}', name: 'get_joueur_id', methods: ['GET'])]
    public function getJoueurId(JoueursRepository $joueursRepository, SerializerInterface $serializer, int $id): Response
    {
        $joueur = $joueursRepository->find($id);
        $joueurJson = $serializer->serialize($joueur, 'json', ['groups' => 'joueur']);

        return new JsonResponse($joueurJson, Response::HTTP_OK, [], true);
    }


    #[Route('/delete/{id}', name: 'delete_joueur', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, JoueursRepository $joueursRepository, int $id): Response
    {
        $joueur = $joueursRepository->find($id);

        $entityManager->remove($joueur);
        $entityManager->flush();

        return new Response(Response::HTTP_ACCEPTED, Response::HTTP_ACCEPTED, [], true);
    }
}
