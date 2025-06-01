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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Store $store = null;

    #[ORM\Column]
    private ?DateTime $order_date = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(length: 3)]
    private ?string $currency = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tracking_courier = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $tracking_url = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'purchase', cascade: ['persist'], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOrderDate(): ?DateTime
    {
        return $this->order_date;
    }

    public function setOrderDate(DateTime $order_date): static
    {
        $this->order_date = $order_date;

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

    public function getTrackingCourier(): ?string
    {
        return $this->tracking_courier;
    }

    public function setTrackingCourier(?string $tracking_courier): static
    {
        $this->tracking_courier = $tracking_courier;

        return $this;
    }

    public function getTrackingUrl(): ?string
    {
        return $this->tracking_url;
    }

    public function setTrackingUrl(?string $tracking_url): static
    {
        $this->tracking_url = $tracking_url;

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
}
