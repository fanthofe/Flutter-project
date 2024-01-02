<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
// security
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
// security entity
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

// two factor auth
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ApiResource(
 * 
 *     paginationEnabled=false,
 * 
 *     formats={"json"},
 * 
 *     collectionOperations={
 *         "get"={
 *             "security"="is_granted('ROLE_USER')"
 *         },
 *         "post"={
 *             "security"="is_granted('ROLE_USER')"
 *         }
 *     },
 * 
 *     itemOperations={
 *         "get"={
 *             "security"="is_granted('ROLE_USER')"
 *         },
 *         "patch" = {
 *              "security"="is_granted('EDIT', object)"
 *         },
 *         "delete"={
 *             "security"="is_granted('ROLE_ADMIN')"
 *         },
 * 
 *         "api_user_register"={
 *             "method"="POST",
 *             "path"="/register",
 *             "controller"=ApiAuthController::class
 *         },
 *     },
 * 
 *     normalizationContext={
 *          "groups"={
 *              "user:read"
 *          }
 *     },
 * 
 *     denormalizationContext={
 *          "groups"={
 *              "user:write"
 *      }},
 * )
 * 
 * @ApiFilter(SearchFilter::class, properties={
 *      "city": "partial"
 * })
 * 
 * @ApiFilter(BooleanFilter::class, properties={
 *      "isProfessional",
 *      "vehicle",
 *      "parent",
 *      "subscriber",
 * })
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Il y a déjà un compte avec cette adresse email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read", "user:write", "chat_message:read", "chat:read", "availability:read"}) 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "user:write"})
     * 
     * @Assert\Email(message="L'adresse email n'est pas valide")
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read", "user:write"})
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:write"})
     * 
     * @Assert\Length(
     *     min=8,
     *     minMessage="Le mot de passe doit contenir au moins 8 caractères"
     * )
     */
    private $password;
    /**
     * @ORM\Column(type="string", length=80)
     * @Groups({"user:read", "user:write","chat:read"})
     * 
     * @Assert\NotBlank(message="Le prénom est obligatoire")
     * @Assert\Length(min=2, minMessage="Le prénom doit contenir au moins 2 caractères")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=80)
     * @Groups({"user:read", "user:write" ,"chat:read"})
     * 
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(min=2, minMessage="Le nom doit contenir au moins 2 caractères")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"user:read", "user:write"})
     *
     * @Assert\Regex(
     *   pattern="/^[a-zA-Z0-9 ,'\-\.\p{l}]*$/u",
     *   message="Votre saisie ne doit pas contenir de caractères spéciaux"
     * )
     * @Assert\Length(min=2, minMessage="Le métier doit contenir au moins 2 caractères")
     */
    private $job;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"user:read", "user:write"})
     *
     * @Assert\PositiveOrZero(message="La valeur doit être positive ou égale à 0")
     * 
     */
    private $experienceDuration;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read", "user:write"})
     */
    private $parent = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read", "user:write"})
     */
    private $subscriber = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"user:read", "user:write"})
     */
    private $isProfessional  = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read", "user:write"})
     */
    private $status = true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read", "user:write"})
     * 
     * @Assert\Regex(
     *   pattern="/^[a-zA-Z0-9 ,'\-\.\p{l}]*$/u",
     *   message="Votre saisie ne doit pas contenir de caractères spéciaux"
     * )
     * @Assert\Length(min=10, minMessage="'adresse' doit contenir au moins 10 caractères")
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"user:read", "user:write"})
     * 
     * @Assert\Regex(
     *   pattern="/^[a-zA-Z0-9 ,'\-\.\p{l}]*$/u",
     *   message="Votre saisie ne doit pas contenir de caractères spéciaux"
     * )
     * @Assert\Length(min=2, minMessage="'ville' doit contenir au moins 2 caractères")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"user:read", "user:write"})
     * 
     * @Assert\Regex(
     * pattern="/^[0-9 ]+$/",
     * message="Votre saisie ne doit contenir que des chiffres ou des espaces"
     * )
     * @Assert\Length(min=5, max=6, minMessage="'code postal' doit contenir au moins 5 chiffres", maxMessage="'code postal' doit contenir au maximum 5 chiffres et un espace Eg: 75 000 ou 75000")
     * 
     */
    private $zip;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"user:read", "user:write"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"user:read", "user:write"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user:read", "user:write"})
     * 
     * @Assert\Regex(
     *   pattern="/^[a-zA-Z0-9 ,'\-\.\p{l}]*$/u",
     *   message="Votre saisie ne doit pas contenir de caractères spéciaux"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read", "user:write"})
     */
    private $profilPicture;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"user:read", "user:write"})
     * 
     * @Assert\Regex(
     *  pattern="/^[0-9]*$/",
     *  message="Votre saisie ne doit contenir que des chiffres"
     * )
     */
    private $hourPrice;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"user:read", "user:write"})
     */
    private $vehicle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"user:read", "user:write"})
     * 
     * @Assert\Regex(
     *  pattern="/^[0-9]*$/",
     *  message="Votre saisie ne doit contenir que des chiffres"
     * )
     */
    private $maxArea;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"user:read", "user:write"})
     * 
     */
    private $birthday;

    /**
     * @ORM\OneToMany(targetEntity=Availability::class, mappedBy="user", cascade={"remove"})
     * @Groups({"user:read", "user:write"})
     * 
     * //@Assert\Valid()
     */
    private $availabilities;

    /**
     * @ORM\ManyToMany(targetEntity=Chat::class, mappedBy="user", cascade={"remove"})
     * @Groups({"user:read", "user:write"})
     * 
     * //@Assert\Valid()
     */
    private $chats;

    /**
     * @ORM\OneToMany(targetEntity=ChatMessage::class, mappedBy="author", cascade={"remove"})
     * 
     * //@Assert\Valid()
     */
    private $chatMessages;

    /**
     * @ORM\OneToMany(targetEntity=Children::class, mappedBy="user", cascade={"remove"})
     * @Groups({"user:read", "user:write"})
     * 
     * //@Assert\Valid()
     */
    private $childrens;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"user:read", "user:write"})
     * 
     * //@Assert\Valid()
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author", orphanRemoval=true)
     * @Groups({"user:read", "user:write"})
     * 
     * //@Assert\Valid()
     */
    private $authorComments;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="subject", orphanRemoval=true)
     * @Groups({"user:read", "user:write"})
     * 
     * //@Assert\Valid()
     */
    private $subjectComments;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $authCode;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Gedmo\Timestampable(on="create")
     * 
     * //@Assert\DateTime(message="La date n'est pas valide")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * 
     * //@Assert\DateTime(message="La date n'est pas valide")
     */
    private $updatedAt;


    public function __construct()
    {
        $this->availabilities = new ArrayCollection();
        $this->chats = new ArrayCollection();
        $this->childrens = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->authorComments = new ArrayCollection();
        $this->subjectComments = new ArrayCollection();
        $this->chatMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getExperienceDuration(): ?int
    {
        return $this->experienceDuration;
    }

    public function setExperienceDuration(?int $experienceDuration): self
    {
        $this->experienceDuration = $experienceDuration;

        return $this;
    }

    public function isParent(): ?bool
    {   
        return $this->parent;
    }

    public function setParent(bool $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function isSubscriber(): ?bool
    {
        return $this->subscriber;
    }

    public function setSubscriber(bool $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    public function isIsProfessional(): ?bool
    {
        return $this->isProfessional;
    }

    public function setIsProfessional(?bool $isProfessional): self
    {
        $this->isProfessional = $isProfessional;

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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

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

    public function getProfilPicture(): ?string
    {
        return $this->profilPicture;
    }

    public function setProfilPicture(?string $profilPicture): self
    {
        $this->profilPicture = $profilPicture;

        return $this;
    }

    public function getHourPrice(): ?string
    {
        return $this->hourPrice;
    }

    public function setHourPrice(?string $hourPrice): self
    {
        $this->hourPrice = $hourPrice;

        return $this;
    }

    public function isVehicle(): ?bool
    {
        return $this->vehicle;
    }

    public function setVehicle(?bool $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getMaxArea(): ?int
    {
        return $this->maxArea;
    }

    public function setMaxArea(?int $maxArea): self
    {
        $this->maxArea = $maxArea;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {

        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

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
     * @return Collection<int, Availability>
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): self
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities[] = $availability;
            $availability->setUser($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): self
    {
        if ($this->availabilities->removeElement($availability)) {
            // set the owning side to null (unless already changed)
            if ($availability->getUser() === $this) {
                $availability->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->addUser($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->removeElement($chat)) {
            $chat->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Children>
     */
    public function getChildrens(): Collection
    {
        return $this->childrens;
    }

    public function addChildren(Children $children): self
    {
        if (!$this->childrens->contains($children)) {
            $this->childrens[] = $children;
            $children->setUser($this);
        }

        return $this;
    }

    public function removeChildren(Children $children): self
    {
        if ($this->childrens->removeElement($children)) {
            // set the owning side to null (unless already changed)
            if ($children->getUser() === $this) {
                $children->setUser(null);
            }
        }

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
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getAuthorComments(): Collection
    {
        return $this->authorComments;
    }

    public function addAuthorComment(Comment $authorComment): self
    {
        if (!$this->authorComments->contains($authorComment)) {
            $this->authorComments[] = $authorComment;
            $authorComment->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthorComment(Comment $authorComment): self
    {
        if ($this->authorComments->removeElement($authorComment)) {
            // set the owning side to null (unless already changed)
            if ($authorComment->getAuthor() === $this) {
                $authorComment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getSubjectComments(): Collection
    {
        return $this->subjectComments;
    }

    public function addSubjectComment(Comment $subjectComment): self
    {
        if (!$this->subjectComments->contains($subjectComment)) {
            $this->subjectComments[] = $subjectComment;
            $subjectComment->setSubject($this);
        }

        return $this;
    }

    public function removeSubjectComment(Comment $subjectComment): self
    {
        if ($this->subjectComments->removeElement($subjectComment)) {
            // set the owning side to null (unless already changed)
            if ($subjectComment->getSubject() === $this) {
                $subjectComment->setSubject(null);
            }
        }

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
            $chatMessage->setAuthor($this);
        }

        return $this;
    }

    public function removeChatMessage(ChatMessage $chatMessage): self
    {
        if ($this->chatMessages->removeElement($chatMessage)) {
            // set the owning side to null (unless already changed)
            if ($chatMessage->getAuthor() === $this) {
                $chatMessage->setAuthor(null);
            }
        }

        return $this;
    }

    // to string (easy admin Association Field)
    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getMercureTopic(): string
    {
        return 'user/' . $this->getId(); // Vous pouvez personnaliser le sujet selon vos besoins
    }

    public function isEmailAuthEnabled(): bool
    {
        return true; // This can be a persisted field to switch email code authentication on/off
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }
}