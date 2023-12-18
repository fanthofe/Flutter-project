<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
// security
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\CommentRepository;
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
 *              "comment:read"
 *          }}, 
 * 
 *     denormalizationContext={
 *          "groups"={
 *              "comment:write"
 *          }} 
 * )
 * 
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"comment:read", "comment:write", "user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"comment:read", "comment:write", "user:read"})
     * 
     * @Assert\NotBlank(message="Le contenu du commentaire ne peut pas être vide")
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"comment:read", "comment:write", "user:read"})
     * 
     * @Assert\NotBlank(message="La note du commentaire ne peut pas être vide")
     */
    private $rate;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"comment:read", "comment:write", "user:read"})
     */
    private $status = true;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="authorComments")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"comment:read", "comment:write"})
     * 
     * @Assert\Valid()
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subjectComments")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"comment:read", "comment:write"})
     * 
     * @Assert\Valid()
     */
    private $subject;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"comment:read", "comment:write", "user:read"})
     * 
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"comment:read", "comment:write"})
     * 
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getSubject(): ?User
    {
        return $this->subject;
    }

    public function setSubject(?User $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
    // to string
    public function __toString()
    {
        return $this->content;
    }

}