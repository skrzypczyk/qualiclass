<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
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

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(nullable: true)]
    private ?int $credit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $goal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $syllabus = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'modules')]
    private Collection $categories;

    #[ORM\Column(nullable: true)]
    private ?bool $isArchived = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isShared = null;

    /**
     * @var Collection<int, Assessment>
     */
    #[ORM\ManyToMany(targetEntity: Assessment::class, inversedBy: 'modules')]
    private Collection $assessments;

    /**
     * @var Collection<int, Assignment>
     */
    #[ORM\OneToMany(targetEntity: Assignment::class, mappedBy: 'module')]
    private Collection $assignments;

    /**
     * @var Collection<int, ModuleCompetenceAssignment>
     */
    #[ORM\OneToMany(targetEntity: ModuleCompetenceAssignment::class, mappedBy: 'module')]
    private Collection $moduleCompetenceAssignments;



    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->assessments = new ArrayCollection();
        $this->assignments = new ArrayCollection();
        $this->moduleCompetenceAssignments = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id?->toRfc4122(); // Uuid|null → string|null
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCredit(): ?int
    {
        return $this->credit;
    }

    public function setCredit(?int $credit): static
    {
        $this->credit = $credit;

        return $this;
    }

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(?string $goal): static
    {
        $this->goal = $goal;

        return $this;
    }

    public function getSyllabus(): ?string
    {
        return $this->syllabus;
    }

    public function setSyllabus(?string $syllabus): static
    {
        $this->syllabus = $syllabus;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(?bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function isShared(): ?bool
    {
        return $this->isShared;
    }

    public function setIsShared(?bool $isShared): static
    {
        $this->isShared = $isShared;

        return $this;
    }

    /**
     * @return Collection<int, Assessment>
     */
    public function getAssessments(): Collection
    {
        return $this->assessments;
    }

    public function addAssessment(Assessment $assessment): static
    {
        if (!$this->assessments->contains($assessment)) {
            $this->assessments->add($assessment);
        }

        return $this;
    }

    public function removeAssessment(Assessment $assessment): static
    {
        $this->assessments->removeElement($assessment);

        return $this;
    }

    /**
     * @return Collection<int, Assignment>
     */
    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function addAssignment(Assignment $assignment): static
    {
        if (!$this->assignments->contains($assignment)) {
            $this->assignments->add($assignment);
            $assignment->setModule($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): static
    {
        if ($this->assignments->removeElement($assignment)) {
            // set the owning side to null (unless already changed)
            if ($assignment->getModule() === $this) {
                $assignment->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ModuleCompetenceAssignment>
     */
    public function getModuleCompetenceAssignments(): Collection
    {
        return $this->moduleCompetenceAssignments;
    }

    public function addModuleCompetenceAssignment(ModuleCompetenceAssignment $moduleCompetenceAssignment): static
    {
        if (!$this->moduleCompetenceAssignments->contains($moduleCompetenceAssignment)) {
            $this->moduleCompetenceAssignments->add($moduleCompetenceAssignment);
            $moduleCompetenceAssignment->setModule($this);
        }

        return $this;
    }

    public function removeModuleCompetenceAssignment(ModuleCompetenceAssignment $moduleCompetenceAssignment): static
    {
        if ($this->moduleCompetenceAssignments->removeElement($moduleCompetenceAssignment)) {
            // set the owning side to null (unless already changed)
            if ($moduleCompetenceAssignment->getModule() === $this) {
                $moduleCompetenceAssignment->setModule(null);
            }
        }

        return $this;
    }


}
