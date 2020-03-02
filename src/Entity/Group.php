<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="groups", indexes={@ORM\Index(name="IDX_6DC044C57E3C61F9", columns={"owner_id"})})
 * @ORM\Entity
 */
class Group {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="groups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * })
     */
    private $owner;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="group_user",
     *   joinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   }
     * )
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct() {
        $this->user = new ArrayCollection();
    }

    /**
     * Gets the ID of the Group
     *
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Gets the name of the Group
     *
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * Updates the name of the Group
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the User who created the Group
     *
     * @return User|null
     */
    public function getOwner(): ?User {
        return $this->owner;
    }

    /**
     * Sets the owner of the Group upon creation
     *
     * @param User|null $owner
     * @return $this
     */
    public function setOwner(?User $owner): self {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Gets the list of Users that are members of the Group
     *
     * @return Collection|User[]
     */
    public function getUser(): Collection {
        return $this->user;
    }

    /**
     * Adds a User to the list of members of the Group
     *
     * @param User $user
     * @return $this
     */
    public function addUser(User $user): self {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    /**
     * Removes a User from the list of members of the Group
     *
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user): self {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
        }

        return $this;
    }

}
