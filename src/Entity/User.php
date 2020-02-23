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

    public function __construct() {
        $this->sentMsg = new ArrayCollection();
        $this->receivedMsg = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->inboxes = new ArrayCollection();
        $this->friendships = new ArrayCollection();
        $this->receivedFriendships = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
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
     * @see UserInterface
     */
    public function getPassword(): string {
        return (string) $this->password;
    }

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
     * @return Collection|Message[]
     */
    public function getSentMsg(): Collection {
        return $this->sentMsg;
    }

    public function addSentMsg(Message $sentMsg): self {
        if (!$this->sentMsg->contains($sentMsg)) {
            $this->sentMsg[] = $sentMsg;
            $sentMsg->setSender($this);
        }

        return $this;
    }

    public function removeSentMsg(Message $sentMsg): self {
        if ($this->sentMsg->contains($sentMsg)) {
            $this->sentMsg->removeElement($sentMsg);
            // set the owning side to null (unless already changed)
            if ($sentMsg->getSender() === $this) {
                $sentMsg->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getReceivedMsg(): Collection {
        return $this->receivedMsg;
    }

    public function addReceivedMsg(Message $receivedMsg): self {
        if (!$this->receivedMsg->contains($receivedMsg)) {
            $this->receivedMsg[] = $receivedMsg;
            $receivedMsg->addReceiver($this);
        }

        return $this;
    }

    public function removeReceivedMsg(Message $receivedMsg): self {
        if ($this->receivedMsg->contains($receivedMsg)) {
            $this->receivedMsg->removeElement($receivedMsg);
            $receivedMsg->removeReceiver($this);
        }

        return $this;
    }

    public function getRecovery(): ?string {
        return $this->recovery;
    }

    public function setRecovery(?string $recovery): self {
        $this->recovery = $recovery;

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection {
        return $this->groups;
    }

    public function addGroup(Group $group): self {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setOwner($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            // set the owning side to null (unless already changed)
            if ($group->getOwner() === $this) {
                $group->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Inbox[]
     */
    public function getInboxes(): Collection {
        return $this->inboxes;
    }

    public function addInbox(Inbox $inbox): self {
        if (!$this->inboxes->contains($inbox)) {
            $this->inboxes[] = $inbox;
            $inbox->setUser($this);
        }

        return $this;
    }

    public function removeInbox(Inbox $inbox): self {
        if ($this->inboxes->contains($inbox)) {
            $this->inboxes->removeElement($inbox);
            // set the owning side to null (unless already changed)
            if ($inbox->getUser() === $this) {
                $inbox->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getFriendships(): Collection {
        return $this->friendships;
    }

    public function addFriendship(Friendship $friendship): self {
        if (!$this->friendships->contains($friendship)) {
            $this->friendships[] = $friendship;
            $friendship->setSender($this);
        }

        return $this;
    }

    public function removeFriendship(Friendship $friendship): self {
        if ($this->friendships->contains($friendship)) {
            $this->friendships->removeElement($friendship);
            // set the owning side to null (unless already changed)
            if ($friendship->getSender() === $this) {
                $friendship->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getReceivedFriendships(): Collection {
        return $this->receivedFriendships;
    }

    public function addReceivedFriendship(Friendship $receivedFriendship): self {
        if (!$this->receivedFriendships->contains($receivedFriendship)) {
            $this->receivedFriendships[] = $receivedFriendship;
            $receivedFriendship->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedFriendship(Friendship $receivedFriendship): self {
        if ($this->receivedFriendships->contains($receivedFriendship)) {
            $this->receivedFriendships->removeElement($receivedFriendship);
            // set the owning side to null (unless already changed)
            if ($receivedFriendship->getReceiver() === $this) {
                $receivedFriendship->setReceiver(null);
            }
        }

        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

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

    public function getFriendRequests() {
        $requests = new ArrayCollection();

        foreach ($this->getReceivedFriendships() as $friendship)
            if ($friendship->isPending())
                $requests->add($friendship);

        return $requests;
    }
}
