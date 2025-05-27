<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
class School
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\ManyToOne(inversedBy: 'schools')]
    private ?User $owner = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDisable = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'School')]
    private Collection $users;

    /**
     * @var Collection<int, Assessment>
     */
    #[ORM\ManyToMany(targetEntity: Assessment::class, mappedBy: 'schools')]
    private Collection $assessments;


    /**
     * @var Collection<int, Diploma>
     */
    #[ORM\ManyToMany(targetEntity: Diploma::class, mappedBy: 'schools')]
    private Collection $diplomas;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'school')]
    private Collection $categories;


    public function __construct()
    {
        $this->userSchools = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->assessments = new ArrayCollection();
        $this->diplomas = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

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

    public function isDisable(): ?bool
    {
        return $this->isDisable;
    }

    public function setIsDisable(?bool $isDisable = false): static
    {
        $this->isDisable = $isDisable;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addSchool($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeSchool($this);
        }

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
            $assessment->addSchool($this);
        }

        return $this;
    }

    public function removeAssessment(Assessment $assessment): static
    {
        if ($this->assessments->removeElement($assessment)) {
            $assessment->removeSchool($this);
        }

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
            $diploma->addSchool($this);
        }

        return $this;
    }

    public function removeDiploma(Diploma $diploma): static
    {
        if ($this->diplomas->removeElement($diploma)) {
            $diploma->removeSchool($this);
        }

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
            $category->setSchool($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getSchool() === $this) {
                $category->setSchool(null);
            }
        }

        return $this;
    }


}
