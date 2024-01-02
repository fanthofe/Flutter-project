<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
// security
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\ChatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 * 
 *     paginationEnabled=false,
 *     
 *     mercure=true,
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
 *         "delete" = {"security"="is_granted('ROLE_USER')"},
 *         "api_user_chats"={
 *             "method"="GET",
 *             "path"="/chats/user/{userId}",
 *             "controller"=ApiChatController::class
 *         },
 *         "api_check_chat"={
 *              "method"="GET",
 *              "path"="/chats/user/{userId}/user/{otherUserId}",
 *              "controller"=ApiChatController::class
 *         }    
 *     },
 * 
 *     normalizationContext={
 *          "groups"={
 *              "chat:read"
 *          },
 * 
 *          "datetime_format"="d-m-Y H:i"
 *      }, 
 * 
 *     denormalizationContext={
 *          "groups"={
 *              "chat:write"
 *          }} 
 * )
 * 
 * @ORM\Entity(repositoryClass=ChatRepository::class)
 */
class Chat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     * @Groups({"chat:read", "chat:write", "user:read", "chat_message:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"chat:read", "chat:write"})
     */
    private $status = true;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="chats")
     * @Groups({"chat:read", "chat:write"})
     * 
     * @Assert\Valid()
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=ChatMessage::class, mappedBy="chat", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "DESC"})
     * 
     * @Groups({"chat:read", "chat:write", "user:read"})
     * 
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide")
     */
    private $chatMessages;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"chat:read", "chat:write"})
     * 
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"chat:read", "chat:write"})
     * 
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;


    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->chatMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getChatMessages(): Collection
    {
        return $this->chatMessages;
    }

    public function addChatMessage(ChatMessage $chatMessage): self
    {
        if (!$this->chatMessages->contains($chatMessage)) {
            $this->chatMessages[] = $chatMessage;
            $chatMessage->setChat($this);
        }

        return $this;
    }

    public function removeChatMessage(ChatMessage $chatMessage): self
    {
        if ($this->chatMessages->removeElement($chatMessage)) {
            // set the owning side to null (unless already changed)
            if ($chatMessage->getChat() === $this) {
                $chatMessage->setChat(null);
            }
        }

        return $this;
    }

    // to string
    public function __toString()
    {
        return  strval($this->id);
    }
}