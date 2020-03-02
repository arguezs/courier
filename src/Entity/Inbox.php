<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InboxRepository")
 */
class Inbox {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="inboxes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message")
     * @ORM\JoinColumn(nullable=false)
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $in_out;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_read;

    /**
     * Gets the ID of the Inbox
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Gets the User the Inbox belongs to
     *
     * @return User|null
     */
    public function getUser(): ?User {
        return $this->user;
    }

    /**
     * Sets the User the Inbox belongs to
     *
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the Message asociated with the Inbox
     *
     * @return Message|null
     */
    public function getMessage(): ?Message {
        return $this->message;
    }

    /**
     * Gets whether the Inbox is read or not
     *
     * @return bool|null
     */
    public function isRead(): ?bool
    {
        return $this->is_read;
    }

    /**
     * Updates the read status of the Inbox.
     *
     * @param bool $is_read
     * @return $this
     */
    public function setIsRead(bool $is_read): self
    {
        $this->is_read = $is_read;

        return $this;
    }
}
