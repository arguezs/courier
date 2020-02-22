<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
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

    public function __construct()
    {
        $this->receiver = new ArrayCollection();
        $this->response = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getReceiver(): Collection
    {
        return $this->receiver;
    }

    public function addReceiver(User $receiver): self
    {
        if (!$this->receiver->contains($receiver)) {
            $this->receiver[] = $receiver;
        }

        return $this;
    }

    public function removeReceiver(User $receiver): self
    {
        if ($this->receiver->contains($receiver)) {
            $this->receiver->removeElement($receiver);
        }

        return $this;
    }

    public function getResponses(): ?self
    {
        return $this->responses;
    }

    public function setResponses(?self $responses): self
    {
        $this->responses = $responses;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getResponse(): Collection
    {
        return $this->response;
    }

    public function addResponse(self $response): self
    {
        if (!$this->response->contains($response)) {
            $this->response[] = $response;
            $response->setResponses($this);
        }

        return $this;
    }

    public function removeResponse(self $response): self
    {
        if ($this->response->contains($response)) {
            $this->response->removeElement($response);
            // set the owning side to null (unless already changed)
            if ($response->getResponses() === $this) {
                $response->setResponses(null);
            }
        }

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function sentWhen(){
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

    public function sentAgo(){
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
