<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
// security
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\ChatMessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 * 
 *     paginationEnabled=false,
 * 
 *     formats={"json"},
 *         
 *     collectionOperations={
 *          "get" = {
 *              "security"="is_granted('ROLE_USER')"
 *          },
 *         "post" = {
 *             "security"="is_granted('ROLE_USER')"
 *         },
 *          "api_post_async_messages"={
 *             "method"="POST",
 *            "path"="/api/async_chat_messages",
 *            "controller"=ApiChatController::class
 *         }
 *      },
 * 
 *     itemOperations={
 *         "get" = {
 *              "security"="is_granted('ROLE_USER')"    
 *         },    
 *         "patch" = {
 *              "security"="is_granted('EDIT', object)"
 *          },
 *         "delete" = {
 *              "security"="is_granted('DELETE', object)"
 *          },
 *     },
 * 
 *     normalizationContext={
 *          "groups"={
 *              "chat_message:read"
 *      },
 * 
 *    "datetime_format"="d-m-Y H:i"
 * 
 *    }, 
 * 
 *     denormalizationContext={
 *          "groups"={
 *              "chat_message:write"
 *      }}, 
 * )
 * 
 * 
 * @ORM\Entity(repositoryClass=ChatMessageRepository::class)
 */
class ChatMessage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"chat_message:read", "chat_message:write", "user:read", "chat:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"chat_message:read", "chat_message:write", "chat:read", "user:read"})
     * 
     * @Assert\NotBlank(message="Le contenu du message ne peut pas être vide")
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"chat_message:read", "chat_message:write"})
     */
    private $status = true;

    /**
     * @ORM\ManyToOne(targetEntity=Chat::class, inversedBy="chatMessages")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"chat_message:read", "chat_message:write"})
     * 
     * @Assert\Valid()
     */
    private $chat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="chatMessages")
     * @Groups({"chat_message:read", "chat_message:write", "chat:read"})
     * 
     * @Assert\Valid()
     */
    private $author;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"chat_message:read", "chat_message:write", "chat:read"})
     * 
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
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

    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    public function setChat(?Chat $chat): self
    {
        $this->chat = $chat;

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

    public function __toString() {
        return $this->author->getFirstName() . ' ' . $this->author->getLastName();
    }
}