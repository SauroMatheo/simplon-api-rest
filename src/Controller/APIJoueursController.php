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
use App\Repository\EquipesRepository;
use App\Entity\Joueurs;


#[Route('/api/joueurs')]
class APIJoueursController extends AbstractController
{
    #[Route(name: 'get_joueurs', methods: ['GET'])]
    public function getJoueurs(
        Request $request,
        JoueursRepository $joueursRepo,
        SerializerInterface $serializer
        ): JsonResponse
    {
        // TODO: Implémenter findLimit, potentiellement la recherche
        $id = $request->get('id');

        if (isset($id)) {
            $joueur = $joueursRepo->find($id);

            if ($joueur === null) {
                return $this->renvoiJson(Response::HTTP_NOT_FOUND, "Entité introuvable");
            }

            $joueursJson = $serializer->serialize($joueur, 'json', ['groups' => 'joueur']);

        } else {
            $joueurs = $joueursRepo->findAll();
            $joueursJson = $serializer->serialize($joueurs, 'json', ['groups' => 'joueursMinimum']);
        }

        return new JsonResponse($joueursJson, Response::HTTP_OK, [], true);
    }


    #[Route(name: 'supprimer_joueur', methods: ['DELETE'])]
    public function supprimer(
        Request $request,
        EntityManagerInterface $entityManager,
        JoueursRepository $joueursRepo
        ): JsonResponse
    {
        if (!$request->query->has('id')) {
            return $this->renvoiJson(Response::HTTP_BAD_REQUEST, "Aucun id spécifié");
        }

        try {
            $id = $request->get('id');
            $joueur = $joueursRepo->find($id);
        } catch (Exception $e) {
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        if ($joueur === null) {
            return $this->renvoiJson(Response::HTTP_NOT_FOUND, "Entité introuvable");
        }

        $entityManager->remove($joueur);
        $entityManager->flush();

        if (null !== $joueursRepo->find($id)) {
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, "L'entité n'a pas pu être supprimée");
        }

        return $this->renvoiJson(Response::HTTP_NO_CONTENT, "Joueur supprimé avec succès");
    }


    #[Route(name: 'ajouter_joueur', methods: ['POST'])]
    public function ajouter(
        Request $request,
        EquipesRepository $equipeRepo,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
        ): JsonResponse
    {
        $requestArray = $request->toArray();

        try {
            // TODO: Re-modifier pour utiliser le deserializer en créant un Normalizer
            if (!isset($requestArray["nom"]) || !isset($requestArray["prenom"])) {
                return $this->renvoiJson(Response::HTTP_BAD_REQUEST, "Le nom ou prenom est manquant");
            }

            $joueur = new Joueurs();
            $joueur->setNom($requestArray["nom"]);
            $joueur->setPrenom($requestArray["prenom"]);
            
            // Un joueur n'a pas forcément une equipe
            if (isset($requestArray["equipe"]) && gettype($requestArray["equipe"]) == "integer") {
                $joueur->setEquipe(
                    $equipeRepo->find($requestArray["equipe"])
                );
            }

        } catch (Exception $e) {
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        $entityManager->persist($joueur);
        $entityManager->flush();


        return new JsonResponse(
            $serializer->serialize($joueur, 'json', ['groups' => 'joueursMinimum']),
            Response::HTTP_CREATED,
            [], true
        );
    }


    #[Route(name: 'update_joueur', methods: ['PUT'])]
    public function modifier(
        Request $request,
        EntityManagerInterface $em,
        JoueursRepository $joueurRepo,
        EquipesRepository $equipeRepo,
        SerializerInterface $serializer
        ): JsonResponse
    {
        $json = json_decode($request->getContent(), true);

        if (!isset($json['id'])) {
            return $this->renvoiJson(Response::HTTP_BAD_REQUEST, "Aucun id spécifié");
        }

        try {
            $joueur = $joueurRepo->find($json['id']);
        } catch (Exception $e) {
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        if ($joueur === null) {
            return $this->renvoiJson(Response::HTTP_NOT_FOUND, "Joueur introuvable");
        }

        try {
            // TODO: Re-modifier pour utiliser le deserializer en créant un Normalizer

            if (isset($json["nom"]) && gettype($json["nom"]) == "string") {
                $joueur->setNom($json["nom"]);
            }

            if (isset($json["prenom"]) && gettype($json["prenom"]) == "string") {
                $joueur->setPrenom($json["prenom"]);
            }

            if (isset($json["equipe"])) {
                $idEquipe = $json["equipe"];

                if (gettype($idEquipe) == "integer") {
                    $equipe = $equipeRepo->find($idEquipe);

                    if ($equipe !== null) { $joueur->setEquipe($equipe); }

                } elseif ($idEquipe == "null") {
                    $joueur->setEquipe(null);
                }
            }

        } catch (Exception $e) {
            return $this->renvoiJson(Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        $em->persist($joueur);
        $em->flush();

        $joueurJson = $serializer->serialize($joueur, 'json', ['groups' => 'joueur']);


        return new JsonResponse($joueurJson, Response::HTTP_OK, [], true);
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
