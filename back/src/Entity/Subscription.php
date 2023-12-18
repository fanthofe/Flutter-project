<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
// security
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 * 
 *     paginationEnabled=false,
 * 
 *     formats={"json"},
 * 
 *     collectionOperations={
 *          "get" = {"security"="is_granted('ROLE_USER')"},
 *          "post" = {"security"="is_granted('ROLE_USER')"}
 *      },
 * 
 *     itemOperations={
 *         "get" = {"security"="is_granted('ROLE_USER')"},
 *         "patch" = {"security"="is_granted('ROLE_USER')"},
 *         "delete" = {"security"="is_granted('ROLE_ADMIN')"}
 *     },
 * 
 *     normalizationContext={
 *          "groups"={
 *              "subscription:read"
 *           }}, 
 * 
 *     denormalizationContext={
 *          "groups"={
 *              "subscription:write"
 *          }} 
 * )
 * 
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"subscription:read", "subscription:write", "user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"subscription:read", "subscription:write", "user:read"})
     * 
     * @Assert\NotBlank(message="Le nom de l'abonnement est obligatoire")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"subscription:read", "subscription:write", "user:read"})
     * 
     * @Assert\NotBlank(message="La durée de l'abonnement est obligatoire")
     */
    private $durationMonth = 0;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"subscription:read", "subscription:write", "user:read"})
     * 
     * @Assert\NotBlank(message="Le prix de l'abonnement est obligatoire")
     */
    private $price = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"subscription:read", "subscription:write", "user:read"})
     * 
     * @Assert\NotBlank(message="La description de l'abonnement est obligatoire")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"subscription:read", "subscription:write", "user:read"})
     */
    private $status = true;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"subscription:read", "subscription:write"})
     * 
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"subscription:read", "subscription:write"})
     * 
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="subscription")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDurationMonth(): ?int
    {
        return $this->durationMonth;
    }

    public function setDurationMonth(int $durationMonth): self
    {
        $this->durationMonth = $durationMonth;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setSubscription($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getSubscription() === $this) {
                $order->setSubscription(null);
            }
        }

        return $this;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setName(?string $name): self
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function __toString()
    {
        return strval($this->id);
    }
}