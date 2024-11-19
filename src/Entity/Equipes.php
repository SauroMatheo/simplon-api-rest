<?php

namespace App\Entity;

use App\Repository\EquipesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipesRepository::class)]
class Equipes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoPath = null;

    /**
     * @var Collection<int, Joueurs>
     */
    #[ORM\OneToMany(targetEntity: Joueurs::class, mappedBy: 'equipe')]
    private Collection $joueurs;

    /**
     * @var Collection<int, Matchs>
     */
    #[ORM\OneToMany(targetEntity: Matchs::class, mappedBy: 'equipeA')]
    private Collection $matchsA;

    /**
     * @var Collection<int, Matchs>
     */
    #[ORM\OneToMany(targetEntity: Matchs::class, mappedBy: 'equipeB')]
    private Collection $matchsB;

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
        $this->matchsA = new ArrayCollection();
        $this->matchsB = new ArrayCollection();
    }

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

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    public function setLogoPath(?string $logoPath): static
    {
        $this->logoPath = $logoPath;

        return $this;
    }

    /**
     * @return Collection<int, Joueurs>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueurs $joueur): static
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs->add($joueur);
            $joueur->setEquipe($this);
        }

        return $this;
    }

    public function removeJoueur(Joueurs $joueur): static
    {
        if ($this->joueurs->removeElement($joueur)) {
            // set the owning side to null (unless already changed)
            if ($joueur->getEquipe() === $this) {
                $joueur->setEquipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Matchs>
     */
    public function getMatchsA(): Collection
    {
        return $this->matchsA;
    }

    public function addMatchsA(Matchs $matchsA): static
    {
        if (!$this->matchsA->contains($matchsA)) {
            $this->matchsA->add($matchsA);
            $matchsA->setEquipeA($this);
        }

        return $this;
    }

    public function removeMatchsA(Matchs $matchsA): static
    {
        if ($this->matchsA->removeElement($matchsA)) {
            // set the owning side to null (unless already changed)
            if ($matchsA->getEquipeA() === $this) {
                $matchsA->setEquipeA(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Matchs>
     */
    public function getMatchsB(): Collection
    {
        return $this->matchsB;
    }

    public function addMatchsB(Matchs $matchsB): static
    {
        if (!$this->matchsB->contains($matchsB)) {
            $this->matchsB->add($matchsB);
            $matchsB->setEquipeB($this);
        }

        return $this;
    }

    public function removeMatchsB(Matchs $matchsB): static
    {
        if ($this->matchsB->removeElement($matchsB)) {
            // set the owning side to null (unless already changed)
            if ($matchsB->getEquipeB() === $this) {
                $matchsB->setEquipeB(null);
            }
        }

        return $this;
    }
}
