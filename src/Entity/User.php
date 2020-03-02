<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="sender")
     */
    private $sentMsg;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Message", mappedBy="receiver")
     */
    private $receivedMsg;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recovery;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Group", mappedBy="owner", orphanRemoval=true)
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Friendship", mappedBy="sender", orphanRemoval=true)
     */
    private $friendships;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Friendship", mappedBy="receiver", orphanRemoval=true)
     */
    private $receivedFriendships;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Inbox", mappedBy="user", orphanRemoval=true)
     */
    private $inboxes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * User constructor.
     */
    public function __construct() {
        $this->sentMsg = new ArrayCollection();
        $this->receivedMsg = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->inboxes = new ArrayCollection();
        $this->friendships = new ArrayCollection();
        $this->receivedFriendships = new ArrayCollection();
    }

    /**
     * Gets the ID of the User
     *
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Returns the email address of the User
     *
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Gets the User's password
     *
     * @return string
     * @see UserInterface
     */
    public function getPassword(): string {
        return (string) $this->password;
    }

    /**
     * Updates the User's password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt() {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection {
        return $this->groups;
    }

    /**
     * @return Collection|Inbox[]
     */
    public function getInboxes(): Collection {
        return $this->inboxes;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getFriendships(): Collection {
        return $this->friendships;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getReceivedFriendships(): Collection {
        return $this->receivedFriendships;
    }

    /**
     * Gets the name of the User
     *
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * Updates the name of the User
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the User's friends
     *
     * @return ArrayCollection
     */
    public function getFriends() {
        $friends = new ArrayCollection();

         foreach ($this->getFriendships() as $friendship)
             if (!$friendship->isPending())
                $friends->add($friendship->getReceiver());

         foreach ($this->getReceivedFriendships() as $friendship)
             if (!$friendship->isPending())
                 $friends->add($friendship->getSender());

         return $friends;
    }

    /**
     * Gets the pending Friendship requests received by the User
     *
     * @return ArrayCollection
     */
    public function getFriendRequests() {
        $requests = new ArrayCollection();

        foreach ($this->getReceivedFriendships() as $friendship)
            if ($friendship->isPending())
                $requests->add($friendship);

        return $requests;
    }

    /**
     * Gets the User's avatar
     *
     * @return string|null
     */
    public function getAvatar(): ?string {
        return $this->avatar;
    }

    /**
     * Updates the User's avatar
     *
     * @param string|null $avatar
     * @return $this
     */
    public function setAvatar(?string $avatar): self {
        $this->avatar = $avatar;

        return $this;
    }
}
