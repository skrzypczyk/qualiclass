<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
class School
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
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $primaryColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secondaryColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typo = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'school')]
    private Collection $users;

    /**
     * @var Collection<int, Subscription>
     */
    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'school')]
    private Collection $subscriptions;

    #[ORM\Column(nullable: true)]
    private ?int $limitUsers = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isFreeAccount = null;

    /**
     * @var Collection<int, Assessment>
     */
    #[ORM\OneToMany(targetEntity: Assessment::class, mappedBy: 'school')]
    private Collection $assessments;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'school')]
    private Collection $categories;

    /**
     * @var Collection<int, Diploma>
     */
    #[ORM\OneToMany(targetEntity: Diploma::class, mappedBy: 'school')]
    private Collection $diplomas;

    /**
     * @var Collection<int, Credit>
     */
    #[ORM\OneToMany(targetEntity: Credit::class, mappedBy: 'school')]
    private Collection $credits;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->assessments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->diplomas = new ArrayCollection();
        $this->credits = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id?->toRfc4122(); // Uuid|null → string|null
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): static
    {
        $this->img = $img;

        return $this;
    }

    public function getPrimaryColor(): ?string
    {
        return $this->primaryColor;
    }

    public function setPrimaryColor(?string $primaryColor): static
    {
        $this->primaryColor = $primaryColor;

        return $this;
    }

    public function getSecondaryColor(): ?string
    {
        return $this->secondaryColor;
    }

    public function setSecondaryColor(?string $secondaryColor): static
    {
        $this->secondaryColor = $secondaryColor;

        return $this;
    }

    public function getTypo(): ?string
    {
        return $this->typo;
    }

    public function setTypo(?string $typo): static
    {
        $this->typo = $typo;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(bool $isActive = null): Collection
    {
        if ($isActive !== null) {
            return $this->users->filter(fn(User $user) => $user->isDisable() !== $isActive);
        }
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setSchool($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSchool() === $this) {
                $user->setSchool(null);
            }
        }

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
            $subscription->setSchool($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getSchool() === $this) {
                $subscription->setSchool(null);
            }
        }

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


    public function getLimitUsers(): ?int
    {
        return $this->limitUsers;
    }

    public function setLimitUsers(?int $limitUsers): static
    {
        $this->limitUsers = $limitUsers;

        return $this;
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
            $assessment->setSchool($this);
        }

        return $this;
    }

    public function removeAssessment(Assessment $assessment): static
    {
        if ($this->assessments->removeElement($assessment)) {
            // set the owning side to null (unless already changed)
            if ($assessment->getSchool() === $this) {
                $assessment->setSchool(null);
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
            $diploma->setSchool($this);
        }

        return $this;
    }

    public function removeDiploma(Diploma $diploma): static
    {
        if ($this->diplomas->removeElement($diploma)) {
            // set the owning side to null (unless already changed)
            if ($diploma->getSchool() === $this) {
                $diploma->setSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Credit>
     */
    public function getCredits(): Collection
    {
        return $this->credits;
    }

    public function addCredit(Credit $credit): static
    {
        if (!$this->credits->contains($credit)) {
            $this->credits->add($credit);
            $credit->setSchool($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): static
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getSchool() === $this) {
                $credit->setSchool(null);
            }
        }

        return $this;
    }



}
