<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FriendshipRepository")
 */
class Friendship {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="friendships")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="receivedFriendships")
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiver;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pending;

    /**
     * Gets the ID of the Friendship
     *
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Gets the User that sent the Friendship in the first place
     *
     * @return User|null
     */
    public function getSender(): ?User {
        return $this->sender;
    }

    /**
     * Sets the User that is sending the Friendship
     *
     * @param User|null $sender
     * @return $this
     */
    public function setSender(?User $sender): self {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Gets the User that received the Friendship in the first place
     *
     * @return User|null
     */
    public function getReceiver(): ?User {
        return $this->receiver;
    }

    /**
     * Sets the User who is receiving the Friendship request.
     *
     * @param User|null $receiver
     * @return $this
     */
    public function setReceiver(?User $receiver): self {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Checks if a Friendship is a pending request or not.
     *
     * @return bool|null
     */
    public function isPending(): ?bool {
        return $this->pending;
    }

    /**
     * Updates the pending state of the Friendship
     *
     * @param bool $pending
     * @return $this
     */
    public function setPending(bool $pending): self {
        $this->pending = $pending;

        return $this;
    }
}
