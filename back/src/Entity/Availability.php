<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
// security
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\AvailabilityRepository;
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
 *         "delete" = {"security"="is_granted('ROLE_ADMIN')"},
 *         "api_user_availabilities"={
 *             "method"="GET",
 *             "path"="availabilities/user/{userId}",
 *             "controller"=ApiAvailabilityController::class
 *         }
 *     },
 * 
 *     normalizationContext={
 *          "groups"={  
 *              "availability:read"
 *          },
 * 
 *          "datetime_format"="Y-m-d H:i"
 *      }, 
 * 
 *     denormalizationContext={
 *          "groups"={
 *              "availability:write"
 *          }} 
 * )
 * @ORM\Entity(repositoryClass=AvailabilityRepository::class)
 */
class Availability
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"availability:read", "availability:write", "user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"availability:read", "availability:write", "user:read"})
     * 
     * @Assert\Length(min=5, minMessage="Ce champ doit contenir au moins 5 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"availability:read", "availability:write", "user:read"})
     * 
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"availability:read", "availability:write", "user:read"})
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide")
     */
    private $endDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"availability:read", "availability:write", "user:read"})
     */
    private $isRecurrent;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"availability:read", "availability:write", "user:read"})
     */
    private $status = true;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="availabilities")
     * @ORM\JoinColumn(nullable=false, name="user_id", referencedColumnName="id")
     * @Groups({"availability:read", "availability:write"})
     * 
     * @Assert\Valid()
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"availability:read"})
     * 
    * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"availability:read"})
     * 
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStartDate(): ?\DateTimeInterface
    {   
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    //to string 
    public function __toString()
    {   
        return $this->name;
    }
}