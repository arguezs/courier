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
     * @return Message|null
     */
    public function getMessage(): ?Message {
        return $this->message;
    }

    public function setMessage(?Message $message): self {
        $this->message = $message;

        return $this;
    }

    public function getInOut(): ?bool {
        return $this->in_out;
    }

    public function setInOut(bool $in_out): self
    {
        $this->in_out = $in_out;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(bool $is_read): self
    {
        $this->is_read = $is_read;

        return $this;
    }
}
