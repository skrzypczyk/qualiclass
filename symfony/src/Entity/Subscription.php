<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $uuid  = null;
    #[ORM\Column(length: 255)]
    private ?string $stripeSubscriptionId = null;

    #[ORM\Column(nullable: true)]
    private ?int $limitUsers = null;

    #[ORM\Column(length: 255)]
    private ?string $stripeCustomerId = null;

    /**
     * @var Collection<int, Invoice>
     */
    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'subscription')]
    private Collection $invoices;

    #[ORM\Column(nullable: true)]
    private ?bool $isUnsubscribed = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    private ?School $school = null;

    #[ORM\Column(nullable: true)]
    private ?bool $chatgpt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $canceledAt = null;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getStripeSubscriptionId(): ?string
    {
        return $this->stripeSubscriptionId;
    }

    public function setStripeSubscriptionId(string $stripeSubscriptionId): static
    {
        $this->stripeSubscriptionId = $stripeSubscriptionId;

        return $this;
    }

    public function getLimitUsers(bool $withBase = false): ?int
    {
        return ($withBase)?$this->limitUsers+4:$this->limitUsers;
    }

    public function setLimitUsers(?int $limitUsers): static
    {
        $this->limitUsers = $limitUsers;

        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(string $stripeCustomerId): static
    {
        $this->stripeCustomerId = $stripeCustomerId;

        return $this;
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
            $invoice->setSubscription($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getSubscription() === $this) {
                $invoice->setSubscription(null);
            }
        }

        return $this;
    }

    public function lastInvoiceValid(): ?Invoice
    {
        $now = (new \DateTimeImmutable())->setTime(0, 0);

        // On filtre les factures ayant une date de paiement
        $paidInvoices = $this->getInvoices()->filter(function (Invoice $invoice) {
            return $invoice->getPaidAt() !== null;
        });

        if ($paidInvoices->isEmpty()) {
            return null;
        }

        /** @var Invoice $lastInvoice */
        $lastInvoice = $paidInvoices->last();
        $paidAt = \DateTimeImmutable::createFromMutable($lastInvoice->getPaidAt())->setTime(0, 0);
        $validUntil = $paidAt->modify('+1 month');

        if ($now >= $paidAt && $now < $validUntil) {
            return $lastInvoice;
        }

        return null;
    }

    public function isUnsubscribed(): ?bool
    {
        return $this->isUnsubscribed;
    }

    public function setIsUnsubscribed(?bool $isUnsubscribed): static
    {
        $this->isUnsubscribed = $isUnsubscribed;

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;

        return $this;
    }

    public function isChatgpt(): ?bool
    {
        return $this->chatgpt;
    }

    public function setChatgpt(?bool $chatgpt): static
    {
        $this->chatgpt = $chatgpt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCanceledAt(): ?\DateTimeImmutable
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(?\DateTimeImmutable $canceledAt): static
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }
}
