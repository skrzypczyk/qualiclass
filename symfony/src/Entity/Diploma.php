<?php

namespace App\Entity;

use App\Repository\DiplomaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiplomaRepository::class)]
class Diploma
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $RNCP = null;

    #[ORM\ManyToOne(inversedBy: 'diplomas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, Competence>
     */
    #[ORM\OneToMany(targetEntity: Competence::class, mappedBy: 'diploma', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $competences;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    /**
     * @var Collection<int, School>
     */
    #[ORM\ManyToMany(targetEntity: School::class, inversedBy: 'diplomas')]
    private Collection $schools;

    /**
     * @var Collection<int, Program>
     */
    #[ORM\ManyToMany(targetEntity: Program::class, mappedBy: 'diplomas')]
    private Collection $programs;

    public function __construct()
    {
        $this->competences = new ArrayCollection();
        $this->schools = new ArrayCollection();
        $this->programs = new ArrayCollection();
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

    public function getRNCP(): ?string
    {
        return $this->RNCP;
    }

    public function setRNCP(string $RNCP): static
    {
        $this->RNCP = $RNCP;

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
     * @return Collection<int, Competence>
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competence $competence): static
    {
        if (!$this->competences->contains($competence)) {
            $this->competences->add($competence);
            $competence->setDiploma($this);
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): static
    {
        if ($this->competences->removeElement($competence)) {
            // set the owning side to null (unless already changed)
            if ($competence->getDiploma() === $this) {
                $competence->setDiploma(null);
            }
        }

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, School>
     */
    public function getSchools(): Collection
    {
        return $this->schools;
    }

    public function addSchool(School $school): static
    {
        if (!$this->schools->contains($school)) {
            $this->schools->add($school);
        }

        return $this;
    }

    public function removeSchool(School $school): static
    {
        $this->schools->removeElement($school);

        return $this;
    }

    /**
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): static
    {
        if (!$this->programs->contains($program)) {
            $this->programs->add($program);
            $program->addDiploma($this);
        }

        return $this;
    }

    public function removeProgram(Program $program): static
    {
        if ($this->programs->removeElement($program)) {
            $program->removeDiploma($this);
        }

        return $this;
    }
}
