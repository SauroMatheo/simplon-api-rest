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

use App\Repository\JoueursRepository;


#[Route('/api/joueurs')]
class APIJoueursController extends AbstractController
{
    #[Route('', name: 'get_joueurs', methods: ['GET'])]
    public function getJoueurs(Request $request, JoueursRepository $joueursRepo, SerializerInterface $serializer): JsonResponse
    {
        (int) $id = $request->query->get('id');

        if (isset($id)) {
            $joueur = $joueursRepo->find($id);

            if ($joueur === null) { return 
                new JsonResponse(
                    json_encode(["status" => "Not Found", "details" => "Entité introuvable"]),
                    Response::HTTP_NOT_FOUND,
                    [], true
                );
            }

            $joueursJson = $serializer->serialize($joueur, 'json', ['groups' => 'joueur']);

        } else {
            $joueurs = $joueursRepo->findAll();
            $joueursJson = $serializer->serialize($joueurs, 'json', ['groups' => 'joueursMinimum']);
        }

        return new JsonResponse($joueursJson, Response::HTTP_OK, [], true);
    }


    #[Route('', name: 'delete_joueur', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, JoueursRepository $joueursRepo): JsonResponse
    {
        if (!isset($id)) { return
            new JsonResponse(
                json_encode(["status" => "Bad Request", "details" => "Aucun id spécifié"]),
                Response::HTTP_BAD_REQUEST,
                [], true
            );
        }

        try {
            (int) $id = $request->query->get('id');
            $joueur = $joueursRepo->find($id);
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

        if ($joueur === null) { return
            new JsonResponse(
                json_encode(["status" => "Not Found", "details" => "Entité introuvable"]),
                Response::HTTP_NOT_FOUND,
                [], true
            );
        }

        $entityManager->remove($joueur);
        $entityManager->flush();

        if (null !== $joueursRepo->find($id)) {
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


    #[Route('', name: 'add_joueur', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
        ): JsonResponse
    {
        try {
            $json = $request->getContent();
            $joueur = $serializer->deserialize(
                $json,
                'App\Entity\Joueurs',
                "json"
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

        $entityManager->persist($joueur);
        $entityManager->flush();


        return new JsonResponse(
            json([
                "status" => "OK",
                "details" => "Joueur créé avec succès"
            ]),
            Response::HTTP_OK,
            [], true
        );
    }


    #[Route('', name: 'update_joueur', methods: ['PUT'])]
    public function modify(
        Request $request,
        EntityManagerInterface $em,
        JoueursRepository $joueurRepo,
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
            $joueur = $joueurRepo->find($json['id']);
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

        if ($joueur === null) { return
            new JsonResponse(
                json_encode(["status" => "Not Found", "details" => "Entité introuvable"]),
                Response::HTTP_NOT_FOUND,
                [], true
            );
        }

        try {
            $serializer->deserialize(
                $request->getContent(),
                'App\Entity\Joueurs',
                "json",
                [AbstractNormalizer::OBJECT_TO_POPULATE => $joueur]
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

        $em->persist($joueur);
        $em->flush();

        $joueurJson = $serializer->serialize($joueur, 'json', ['groups' => 'joueur']);


        return new JsonResponse($joueurJson, Response::HTTP_OK, [], true);
    }
}
