<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Exception;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $about;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sentMsg")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\Column(type="string", length=10000)
     */
    private $body;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="receivedMsg")
     */
    private $receiver;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="response")
     */
    private $responses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="responses")
     */
    private $response;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * Message constructor.
     */
    public function __construct() {
        $this->receiver = new ArrayCollection();
        $this->response = new ArrayCollection();
    }

    /**
     * Gets the ID of the Message
     *
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Gets the about of the Message
     *
     * @return string|null
     */
    public function getAbout(): ?string {
        return $this->about;
    }

    /**
     * Sets the about of the Message
     *
     * @param string $about
     * @return $this
     */
    public function setAbout(string $about): self {
        $this->about = strip_tags($about);

        return $this;
    }

    /**
     * Gets the sender of the Message
     *
     * @return User|null
     */
    public function getSender(): ?User {
        return $this->sender;
    }

    /**
     * Sets the sender of the Message
     *
     * @param User|null $sender
     * @return $this
     */
    public function setSender(?User $sender): self {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Gets the body of the Message
     *
     * @return string|null
     */
    public function getBody(): ?string {
        return $this->body;
    }

    /**
     * Sets the body of the Message
     *
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): self {
        $this->body = strip_tags($body);

        return $this;
    }

    /**
     * Gets the list of all the Users the Message is sent to
     *
     * @return Collection|User[]
     */
    public function getReceiver(): Collection {
        return $this->receiver;
    }

    /**
     * Adds a User to the list of Users that receive the Message
     *
     * @param User $receiver
     * @return $this
     */
    public function addReceiver(User $receiver): self {
        if (!$this->receiver->contains($receiver)) {
            $this->receiver[] = $receiver;
        }

        return $this;
    }

    /**
     * Removes a User from the list of Users that receive the Message
     *
     * @param User $receiver
     * @return $this
     */
    public function removeReceiver(User $receiver): self {
        if ($this->receiver->contains($receiver)) {
            $this->receiver->removeElement($receiver);
        }

        return $this;
    }

    /**
     * Gets the Message this Message is a Reply to, if it is.
     *
     * @return $this|null
     */
    public function getReplyTo(): ?self {
        return $this->responses;
    }

    /**
     * Sets the Message this Message is a reply to.
     * @param Message|null $responses
     * @return $this
     */
    public function setReplyTo(?self $responses): self {
        $this->responses = $responses;

        return $this;
    }

    /**
     * Gets all the Messages that are a reply to this Message.
     *
     * @return Collection|self[]
     */
    public function getReplies(): Collection {
        return $this->response;
    }

    /**
     * Adds a reply to this Message
     *
     * @param Message $response
     * @return $this
     */
    public function addReply(self $response): self {
        if (!$this->response->contains($response)) {
            $this->response[] = $response;
            $response->setReplyTo($this);
        }

        return $this;
    }

    /**
     * Removes a reply to this Message
     *
     * @param Message $response
     * @return $this
     */
    public function removeReply(self $response): self {
        if ($this->response->contains($response)) {
            $this->response->removeElement($response);
            // set the owning side to null (unless already changed)
            if ($response->getReplyTo() === $this) {
                $response->setReplyTo(null);
            }
        }

        return $this;
    }

    /**
     * Gets the date when the Message was sent
     *
     * @return DateTimeInterface|null
     */
    public function getDate(): ?DateTimeInterface {
        return $this->date;
    }

    /**
     * Sets the date when the Message was sent
     *
     * @param DateTimeInterface $date
     * @return $this
     */
    public function setDate(DateTimeInterface $date): self {
        $this->date = $date;

        return $this;
    }

    /**
     * Returns a visualization of when a Message was sent.
     *  - If it was less than a day ago, the time of the day.
     *  - If it was less than a year ago, the day and the month.
     *  - If ti was more than a year ago, the month and the year.
     *
     * @return false|string
     * @throws Exception
     */
    public function sentWhen() {
        $date = $this->getDate();
        $today = new DateTime('NOW');

        $diff = $today->diff($date);

        if ($diff->y > 0 || $diff->m > 0)
            return date('M y', $date->getTimestamp());
        elseif($diff->d > 0)
            return date('d M', $date->getTimestamp());
        else
            return date('H:i', $date->getTimestamp());
    }

    /**
     * Returns a visualization of how long ago a Message was sent
     *  - If it was more than a year ago, how many years
     *  - If it was more than a month ago, how many months
     *  - If it was more than a day ago, how many days.
     *  - If it was more than an hour ago, how many hours.
     *  - If it was less than an hour ago, how many minutes.
     *
     * @return string
     * @throws Exception
     */
    public function sentAgo() {
        $today = new DateTime('NOW');

        $diff = $today->diff($this->getDate());

        if ($diff->y > 0)
            return $diff->format('%y year' . ($diff->y>1?'s':''));
        elseif ($diff->m > 0)
            return $diff->format('%m month' . ($diff->m>1?'s':''));
        elseif ($diff->d > 0)
            return $diff->format('%d day' . ($diff->d>1?'s':''));
        elseif ($diff->h > 0)
            return $diff->format('%h hour' . ($diff->h>1?'s':''));
        else
            return $diff->format('%i minute' . ($diff->i>1?'s':''));
    }
}
