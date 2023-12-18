<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
// security
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\ChildrenRepository;
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
 *              "children:read"
 *          }}, 
 * 
 *     denormalizationContext={
 *          "groups"={
 *              "children:write"
 *          }} 
 * )
 * 
 * @ORM\Entity(repositoryClass=ChildrenRepository::class)
 */
class Children
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"children:read", "children:write", "user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"children:read", "children:write", "user:read"})
     * 
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"children:read", "children:write", "user:read"})
     * 
     * @Assert\Length(max=20, maxMessage="Le genre ne peut pas dépasser {{ limit }} caractères")
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     * @Groups({"children:read", "children:write", "user:read"})
     * 
     * @Assert\Length(max=80, maxMessage="Le prénom ne peut pas dépasser {{ limit }} caractères")
     */
    private $firstName;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"children:read", "children:write", "user:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"children:read", "children:write", "user:read"})
     */
    private $status = true;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="childrens")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"children:read", "children:write"})
     * 
     * @Assert\Valid()
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"children:read", "children:write"})
     * 
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"children:read", "children:write"})
     * 
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}