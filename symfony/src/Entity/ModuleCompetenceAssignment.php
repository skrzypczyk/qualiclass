<?php

namespace App\Entity;

use App\Repository\ModuleCompetenceAssignmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ModuleCompetenceAssignmentRepository::class)]
class ModuleCompetenceAssignment
{
    /*
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    */

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'moduleCompetenceAssignments')]
    private ?Program $program = null;

    #[ORM\ManyToOne(inversedBy: 'moduleCompetenceAssignments')]
    private ?Competence $competence = null;

    #[ORM\ManyToOne(inversedBy: 'moduleCompetenceAssignments')]
    private ?Module $module = null;

    #[ORM\ManyToOne(inversedBy: 'moduleCompetenceAssignments')]
    private ?Diploma $diploma = null;

    public function getId(): ?string
    {
        return $this->id?->toRfc4122(); // Uuid|null → string|null
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

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): static
    {
        $this->competence = $competence;

        return $this;
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

    public function getDiploma(): ?Diploma
    {
        return $this->diploma;
    }

    public function setDiploma(?Diploma $diploma): static
    {
        $this->diploma = $diploma;

        return $this;
    }
}
