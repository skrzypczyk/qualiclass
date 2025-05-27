<?php

namespace App\Entity;

use App\Repository\ModuleCompetenceAffectationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleCompetenceAffectationRepository::class)]
class ModuleCompetenceAffectation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'moduleCompetenceAffectations')]
    private ?Module $module = null;

    #[ORM\ManyToOne(inversedBy: 'moduleCompetenceAffectations')]
    private ?Competence $competence = null;

    #[ORM\ManyToOne(inversedBy: 'moduleCompetenceAffectations')]
    private ?Program $program = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): static
    {
        $this->competence = $competence;

        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): static
    {
        $this->program = $program;

        return $this;
    }
}
