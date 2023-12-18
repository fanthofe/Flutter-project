<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
// security
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\OrderRepository;
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
 *              "order:read"
 *          }}, 
 * 
 *     denormalizationContext={
 *          "groups"={  
 *              "order:write"
 *          }} 
 * )
 * 
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"order:read", "order:write", "user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     * @Groups({"order:read", "order:write", "user:read"})
     * 
     * @Assert\NotBlank(message="Un type de paiement est requis")
     */
    private $paymentType = '';

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"order:read", "order:write", "user:read"})
     */
    private $status = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"order:read", "order:write", "user:read"})
     */
    private $isRecurrent;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"order:read", "order:write"})
     * 
     * @Assert\NotBlank(message="Un utilisateur est requis")
     * 
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Subscription::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"order:read", "order:write", "user:read"})
     * 
     * @Assert\NotBlank(message="Un abonnement est requis")
     * @Assert\Valid()
     */
    private $subscription;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"order:read", "order:write", "user:read"})
     * 
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"order:read", "order:write"})
     * 
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): self
    {
        $this->paymentType = $paymentType;

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

    public function isIsRecurrent(): ?bool
    {
        return $this->isRecurrent;
    }

    public function setIsRecurrent(?bool $isRecurrent): self
    {
        $this->isRecurrent = $isRecurrent;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }
    // to string
    public function __toString()
    {
        return strval($this->id);
    }
}