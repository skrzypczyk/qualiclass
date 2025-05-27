<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var Collection<int, Diploma>
     */
    #[ORM\ManyToMany(targetEntity: Diploma::class, inversedBy: 'programs')]
    private Collection $diplomas;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $prerequisites = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $goals = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(inversedBy: 'programs')]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'program', targetEntity: ModuleCompetenceAffectation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $affectations;


    public function __construct()
    {
        $this->diplomas = new ArrayCollection();
        $this->affectations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Diploma>
     */
    public function getDiplomas(): Collection
    {
        return $this->diplomas;
    }

    public function addDiploma(Diploma $diploma): static
    {
        if (!$this->diplomas->contains($diploma)) {
            $this->diplomas->add($diploma);
        }

        return $this;
    }

    public function removeDiploma(Diploma $diploma): static
    {
        $this->diplomas->removeElement($diploma);

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getPrerequisites(): ?string
    {
        return $this->prerequisites;
    }

    public function setPrerequisites(string $prerequisites): static
    {
        $this->prerequisites = $prerequisites;

        return $this;
    }

    public function getGoals(): ?string
    {
        return $this->goals;
    }

    public function setGoals(string $goals): static
    {
        $this->goals = $goals;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, ModuleCompetenceAffectation>
     */
    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(ModuleCompetenceAffectation $affectation): static
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations->add($affectation);
            $affectation->setProgram($this);
        }

        return $this;
    }

    public function removeAffectation(ModuleCompetenceAffectation $affectation): static
    {
        if ($this->affectations->removeElement($affectation)) {
            // set the owning side to null (unless already changed)
            if ($affectation->getProgram() === $this) {
                $affectation->setProgram(null);
            }
        }

        return $this;
    }


}
