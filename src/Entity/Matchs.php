<?php

namespace App\Entity;

use App\Repository\MatchsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchsRepository::class)]
class Matchs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'matchsA')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipes $equipeA = null;

    #[ORM\ManyToOne(inversedBy: 'matchsB')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipes $equipeB = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $scoreA = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $scoreB = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipeA(): ?Equipes
    {
        return $this->equipeA;
    }

    public function setEquipeA(?Equipes $equipeA): static
    {
        $this->equipeA = $equipeA;

        return $this;
    }

    public function getEquipeB(): ?Equipes
    {
        return $this->equipeB;
    }

    public function setEquipeB(?Equipes $equipeB): static
    {
        $this->equipeB = $equipeB;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getScoreA(): ?int
    {
        return $this->scoreA;
    }

    public function setScoreA(?int $scoreA): static
    {
        $this->scoreA = $scoreA;

        return $this;
    }

    public function getScoreB(): ?int
    {
        return $this->scoreB;
    }

    public function setScoreB(?int $scoreB): static
    {
        $this->scoreB = $scoreB;

        return $this;
    }
}
