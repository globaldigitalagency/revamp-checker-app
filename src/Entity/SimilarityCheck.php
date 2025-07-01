<?php

namespace App\Entity;

use App\Repository\SimilarityCheckRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SimilarityCheckRepository::class)]
class SimilarityCheck
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4)]
    private ?string $yearFrom = null;

    #[ORM\Column(length: 4)]
    private ?string $yearTo = null;

    #[ORM\Column]
    private ?bool $isRevamp = null;

    #[ORM\ManyToOne(inversedBy: 'similarityChecks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RevampScan $revampScan = null;

    #[ORM\Column]
    private ?float $similarityRate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYearFrom(): ?string
    {
        return $this->yearFrom;
    }

    public function setYearFrom(string $yearFrom): static
    {
        $this->yearFrom = $yearFrom;

        return $this;
    }

    public function getYearTo(): ?string
    {
        return $this->yearTo;
    }

    public function setYearTo(string $yearTo): static
    {
        $this->yearTo = $yearTo;

        return $this;
    }

    public function isRevamp(): ?bool
    {
        return $this->isRevamp;
    }

    public function setIsRevamp(bool $isRevamp): static
    {
        $this->isRevamp = $isRevamp;

        return $this;
    }

    public function getRevampScan(): ?RevampScan
    {
        return $this->revampScan;
    }

    public function setRevampScan(?RevampScan $revampScan): static
    {
        $this->revampScan = $revampScan;

        return $this;
    }

    public function getSimilarityRate(): ?float
    {
        return $this->similarityRate;
    }

    public function setSimilarityRate(float $similarityRate): static
    {
        $this->similarityRate = $similarityRate;

        return $this;
    }
}
