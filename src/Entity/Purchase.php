<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[ORM\Table(name: 'purchase')]
class Purchase extends Audit
{
    const STATUS_ACKNOWLEDGED = 'ACKNOWLEDGED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_DISPATCHED = 'DISPATCHED';
    const STATUS_OUT_FOR_DELIVERY = 'OUT FOR DELIVERY';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Store $store = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $orderId = null;

    #[ORM\Column]
    private ?DateTime $orderDate = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(length: 3)]
    private ?string $currency = null;

    #[ORM\Column(length: 12)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trackingCourier = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $trackingUrl = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'purchase', cascade: ['persist'], orphanRemoval: true)]
    private Collection $items;

    /**
     * @var Collection<int, InboundEmail>
     */
    #[ORM\OneToMany(targetEntity: InboundEmail::class, mappedBy: 'purchase', cascade: ['persist'])]
    private Collection $inboundEmails;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->inboundEmails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): static
    {
        $this->store = $store;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): static
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getOrderDate(): ?DateTime
    {
        return $this->orderDate;
    }

    public function setOrderDate(DateTime $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTrackingCourier(): ?string
    {
        return $this->trackingCourier;
    }

    public function setTrackingCourier(?string $trackingCourier): static
    {
        $this->trackingCourier = $trackingCourier;

        return $this;
    }

    public function getTrackingUrl(): ?string
    {
        return $this->trackingUrl;
    }

    public function setTrackingUrl(?string $trackingUrl): static
    {
        $this->trackingUrl = $trackingUrl;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setPurchase($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getPurchase() === $this) {
                $item->setPurchase(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InboundEmail>
     */
    public function getInboundEmails(): Collection
    {
        return $this->inboundEmails;
    }

    public function addInboundEmail(InboundEmail $inboundEmail): static
    {
        if (!$this->inboundEmails->contains($inboundEmail)) {
            $this->inboundEmails->add($inboundEmail);
            $inboundEmail->setPurchase($this);
        }

        return $this;
    }

    public function removeInboundEmail(InboundEmail $inboundEmail): static
    {
        if ($this->inboundEmails->removeElement($inboundEmail)) {
            // set the owning side to null (unless already changed)
            if ($inboundEmail->getPurchase() === $this) {
                $inboundEmail->setPurchase(null);
            }
        }

        return $this;
    }
}
