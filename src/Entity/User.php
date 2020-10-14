<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\Email
     */
    private ?string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\Length(min=3, max=32)
     */
    private ?string $password;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     */
    private ?string $firstName;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     */
    private ?string $lastName;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\Range(min="now -100 years", max="now", maxMessage="Inccorect date", minMessage="Inccorect date")
     */
    private ?DateTime $birthday;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\Choice(choices={User::GENDER_MALE, User::GENDER_FEMALE})
     */
    private ?string $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $confirmEmail;

    /**
     * @ORM\Column(type="date")
     */
    private ?DateTime $createdAt;

    /**
     * @var Collection|Contact[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Contact", inversedBy="usedUsers")
     */
    private Collection $favoriteContacts;

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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return DateTime|null
     */
    public function getBirthday(): ?DateTime
    {
        return $this->birthday;
    }

    /**
     * @param DateTime|null $birthday
     */
    public function setBirthday(?DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     */
    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return string|null
     */
    public function getConfirmEmail(): ?string
    {
        return $this->confirmEmail;
    }

    /**
     * @param string|null $confirmEmail
     */
    public function setConfirmEmail(?string $confirmEmail): void
    {
        $this->confirmEmail = $confirmEmail;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     */
    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getFavoriteContacts()
    {
        return $this->favoriteContacts;
    }

    /**
     * @param Contact[]|Collection $favoriteContacts
     */
    public function setFavoriteContacts($favoriteContacts): void
    {
        $this->favoriteContacts = $favoriteContacts;
    }

    /**
     * @param Contact $favoriteContact
     */
    public function addFavoriteContact(Contact $favoriteContact): void
    {
        $this->favoriteContacts->add($favoriteContact);
    }

    /**
     * @param Contact $favoriteContact
     */
    public function removeFavoriteContact(Contact $favoriteContact): void
    {
        $this->favoriteContacts->removeElement($favoriteContact);
    }

    /**
     * @param Contact $favoriteContact
     *
     * @return bool
     */
    public function hasFavoriteContact(Contact $favoriteContact): bool
    {
        return $this->favoriteContacts->contains($favoriteContact);
    }
}
