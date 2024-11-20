<?php

namespace App\Entity;

use App\Repository\JoueursRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups; 

#[ORM\Entity(repositoryClass: JoueursRepository::class)]
class Joueurs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["equipe", "joueur"])]
    private ?int $id = null;

    #[Groups(["joueur"])]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Groups(["joueur"])]
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[Groups(["joueur"])]
    #[ORM\ManyToOne(inversedBy: 'joueurs')]
    private ?Equipes $equipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEquipe(): ?Equipes
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipes $equipe): static
    {
        $this->equipe = $equipe;

        return $this;
    }
}
