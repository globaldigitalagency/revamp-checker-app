<?php

namespace App\Entity;

use App\Repository\RevampScanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RevampScanRepository::class)]
class RevampScan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $url = null;

    /**
     * @var Collection<int, SimilarityCheck>
     */
    #[ORM\OneToMany(targetEntity: SimilarityCheck::class, mappedBy: 'revampScan', orphanRemoval: true)]
    private Collection $similarityChecks;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $loadingChecks = false;

    public function __construct()
    {
        $this->similarityChecks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection<int, SimilarityCheck>
     */
    public function getSimilarityChecks(): Collection
    {
        return $this->similarityChecks;
    }

    public function addSimilarityCheck(SimilarityCheck $similarityCheck): static
    {
        if (!$this->similarityChecks->contains($similarityCheck)) {
            $this->similarityChecks->add($similarityCheck);
            $similarityCheck->setRevampScan($this);
        }

        return $this;
    }

    public function removeSimilarityCheck(SimilarityCheck $similarityCheck): static
    {
        if ($this->similarityChecks->removeElement($similarityCheck)) {
            // set the owning side to null (unless already changed)
            if ($similarityCheck->getRevampScan() === $this) {
                $similarityCheck->setRevampScan(null);
            }
        }

        return $this;
    }

    public function isLoadingChecks(): ?bool
    {
        return $this->loadingChecks;
    }

    public function setLoadingChecks(bool $loadingChecks): static
    {
        $this->loadingChecks = $loadingChecks;

        return $this;
    }
}
