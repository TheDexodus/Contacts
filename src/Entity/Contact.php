<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="contacts")
 */
class Contact
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $name;

    /**
     * @var Collection|Communication[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Communication", mappedBy="contact", cascade={"persist", "remove"})
     */
    private Collection $communications;

    /**
     * @var Collection|User[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="favoriteContacts")
     */
    private Collection $usedUsers;

    /**
     * Contact constructor.
     */
    public function __construct()
    {
        $this->communications = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Communication[]|Collection
     */
    public function getCommunications()
    {
        return $this->communications;
    }

    /**
     * @param Communication[]|Collection $communications
     */
    public function setCommunications($communications): void
    {
        $this->communications = $communications;
    }

    /**
     * @param Communication $communication
     */
    public function addCommunication(Communication $communication): void
    {
        $this->communications->add($communication);
    }

    /**
     * @return User[]|Collection
     */
    public function getUsedUsers()
    {
        return $this->usedUsers;
    }

    /**
     * @param User[]|Collection $usedUsers
     */
    public function setUsedUsers($usedUsers): void
    {
        $this->usedUsers = $usedUsers;
    }
}
