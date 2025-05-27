<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cette adresse email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    /**
     * @var Collection<int, Invoice>
     */
    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'owner')]
    private Collection $invoices;

    #[ORM\Column(nullable: true)]
    private ?int $limitSchools = null;

    #[ORM\Column(nullable: true)]
    private ?int $limitUsers = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isFreeAccount = null;


    /**
     * @var Collection<int, School>
     */
    #[ORM\OneToMany(targetEntity: School::class, mappedBy: 'owner')]
    private Collection $schools;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSubscriptionId = null;

    /**
     * @var Collection<int, Subscription>
     */
    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'owner')]
    private Collection $subscriptions;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'users')]
    private ?self $owner = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'owner')]
    private Collection $users;

    #[ORM\Column(nullable: true)]
    private ?bool $isDisable = null;

    /**
     * @var Collection<int, School>
     */
    #[ORM\ManyToMany(targetEntity: School::class, inversedBy: 'users')]
    private Collection $School;

    /**
     * @var Collection<int, Module>
     */
    #[ORM\OneToMany(targetEntity: Module::class, mappedBy: 'owner')]
    private Collection $modules;

    /**
     * @var Collection<int, Assessment>
     */
    #[ORM\OneToMany(targetEntity: Assessment::class, mappedBy: 'owner')]
    private Collection $assessments;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'owner')]
    private Collection $categories;

    /**
     * @var Collection<int, Diploma>
     */
    #[ORM\OneToMany(targetEntity: Diploma::class, mappedBy: 'owner')]
    private Collection $diplomas;

    /**
     * @var Collection<int, Program>
     */
    #[ORM\OneToMany(targetEntity: Program::class, mappedBy: 'owner')]
    private Collection $programs;


    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->schools = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->School = new ArrayCollection();
        $this->modules = new ArrayCollection();
        $this->assessments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->diplomas = new ArrayCollection();
        $this->programs = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function isSuperAdmin(): bool
    {
        return in_array("ROLE_SUPER_ADMIN", $this->getRoles(), true);
    }

    public function isAdmin(): bool
    {
        return in_array("ROLE_ADMIN", $this->getRoles(), true);
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setOwner($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getOwner() === $this) {
                $invoice->setOwner(null);
            }
        }

        return $this;
    }

    public function getlimitSchools(): ?int
    {
        return $this->limitSchools;
    }

    public function setlimitSchools(?int $limitSchools): static
    {
        $this->limitSchools = $limitSchools;

        return $this;
    }

    public function getLimitUsers(): ?int
    {
        return $this->limitUsers;
    }

    public function setLimitUsers(?int $limitUsers): static
    {
        $this->limitUsers = $limitUsers;

        return $this;
    }


    public function getLastSubscription(): ?Subscription
    {
        // On récupère l'abonnement actif (celui qui a des factures)
        $subscription = $this->getSubscriptions()
            ->filter(fn(Subscription $sub) => !$sub->getInvoices()->isEmpty())
            ->last();
        if (!$subscription) {
            return null;
        }
        return $subscription;
    }

    public function getLastInvoiceValid(): ?Invoice
    {
        $now = (new \DateTimeImmutable())->setTime(0, 0);
        // On récupère l'abonnement actif (celui qui a des factures)
        $subscription = $this->getSubscriptions()
            ->filter(fn(Subscription $sub) => !$sub->getInvoices()->isEmpty())
            ->last();
        if (!$subscription) {
            return null;
        }
        // On récupère la dernière facture payée
        $invoices = $subscription->getInvoices()->filter(fn(Invoice $invoice) => $invoice->getPaidAt() !== null);
        if ($invoices->isEmpty()) {
            return null;
        }
        /** @var Invoice $lastInvoice */
        $lastInvoice = $invoices->last();
        $paidAt = \DateTimeImmutable::createFromMutable($lastInvoice->getPaidAt())->setTime(0, 0);
        $validUntil = $paidAt->modify('+1 month');
        return ($now >= $paidAt && $now < $validUntil) ? $lastInvoice : null;
    }

    public function isFreeAccount(): ?bool
    {
        return $this->isFreeAccount;
    }

    public function setIsFreeAccount(?bool $isFreeAccount): static
    {
        $this->isFreeAccount = $isFreeAccount;

        return $this;
    }

    /**
     * @return Collection<int, School>
     */
    public function getSchools(?bool $onlyEnable = false): Collection
    {
        if ($onlyEnable) {
            return $this->schools->filter(fn(School $school) => !$school->isDisable());
        }
        return $this->schools;
    }

    public function addSchool(School $school): static
    {
        if (!$this->schools->contains($school)) {
            $this->schools->add($school);
            $school->setOwner($this);
        }

        return $this;
    }

    public function removeSchool(School $school): static
    {
        if ($this->schools->removeElement($school)) {
            // set the owning side to null (unless already changed)
            if ($school->getOwner() === $this) {
                $school->setOwner(null);
            }
        }

        return $this;
    }

    public function getStripeSubscriptionId(): ?string
    {
        return $this->stripeSubscriptionId;
    }

    public function setStripeSubscriptionId(?string $stripeSubscriptionId): static
    {
        $this->stripeSubscriptionId = $stripeSubscriptionId;

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setOwner($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getOwner() === $this) {
                $subscription->setOwner(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?self
    {
        return $this->owner;
    }

    public function setOwner(?self $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsers(?bool $onlyEnable = false): Collection
    {
        if ($onlyEnable) {
            return $this->users->filter(fn(self $user) => !$user->isDisable());
        }
        return $this->users;
    }

    public function addUser(self $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setOwner($this);
        }

        return $this;
    }

    public function removeUser(self $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getOwner() === $this) {
                $user->setOwner(null);
            }
        }

        return $this;
    }

    public function isDisable(): ?bool
    {
        return $this->isDisable;
    }

    public function setIsDisable(?bool $isDisable): static
    {
        $this->isDisable = $isDisable;

        return $this;
    }

    /**
     * @return Collection<int, School>
     */
    public function getSchool(): Collection
    {
        return $this->School;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): static
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setOwner($this);
        }

        return $this;
    }

    public function removeModule(Module $module): static
    {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getOwner() === $this) {
                $module->setOwner(null);
            }
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
            $assessment->setOwner($this);
        }

        return $this;
    }

    public function removeAssessment(Assessment $assessment): static
    {
        if ($this->assessments->removeElement($assessment)) {
            // set the owning side to null (unless already changed)
            if ($assessment->getOwner() === $this) {
                $assessment->setOwner(null);
            }
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
            $category->setOwner($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getOwner() === $this) {
                $category->setOwner(null);
            }
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
            $diploma->setOwner($this);
        }

        return $this;
    }

    public function removeDiploma(Diploma $diploma): static
    {
        if ($this->diplomas->removeElement($diploma)) {
            // set the owning side to null (unless already changed)
            if ($diploma->getOwner() === $this) {
                $diploma->setOwner(null);
            }
        }

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
            $program->setOwner($this);
        }

        return $this;
    }

    public function removeProgram(Program $program): static
    {
        if ($this->programs->removeElement($program)) {
            // set the owning side to null (unless already changed)
            if ($program->getOwner() === $this) {
                $program->setOwner(null);
            }
        }

        return $this;
    }


}
