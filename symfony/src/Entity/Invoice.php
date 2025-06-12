<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $uuid  = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $stripeInvoiceId;

    #[ORM\Column]
    private int $amount; // en centimes

    #[ORM\Column(length: 10)]
    private string $currency;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $paidAt;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $invoiceUrl = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?Subscription $subscription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStripeInvoiceId(): string
    {
        return $this->stripeInvoiceId;
    }

    public function setStripeInvoiceId(string $stripeInvoiceId): self
    {
        $this->stripeInvoiceId = $stripeInvoiceId;
        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getPaidAt(): \DateTimeInterface
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeInterface $paidAt): self
    {
        $this->paidAt = $paidAt;
        return $this;
    }

    public function getInvoiceUrl(): ?string
    {
        return $this->invoiceUrl;
    }

    public function setInvoiceUrl(?string $invoiceUrl): static
    {
        $this->invoiceUrl = $invoiceUrl;

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): static
    {
        $this->subscription = $subscription;

        return $this;
    }
}
