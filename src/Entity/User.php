<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=false)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="simple_array", length=0, nullable=false)
     */
    private $roles;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="SocialNetwork", mappedBy="user")
     */
    private $socialNetwork;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Approval", mappedBy="user", cascade={"persist", "remove"})
     */
    private $approval;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Publication", mappedBy="user", cascade={"persist", "remove"})
     */
    private $publication;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->socialNetwork = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|SocialNetwork[]
     */
    public function getSocialNetwork(): Collection
    {
        return $this->socialNetwork;
    }

    public function addSocialNetwork(SocialNetwork $socialNetwork): self
    {
        if (!$this->socialNetwork->contains($socialNetwork)) {
            $this->socialNetwork[] = $socialNetwork;
            $socialNetwork->addUser($this);
        }

        return $this;
    }

    public function removeSocialNetwork(SocialNetwork $socialNetwork): self
    {
        if ($this->socialNetwork->contains($socialNetwork)) {
            $this->socialNetwork->removeElement($socialNetwork);
            $socialNetwork->removeUser($this);
        }

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }




    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getApproval(): ?Approval
    {
        return $this->approval;
    }

    public function setApproval(Approval $approval): self
    {
        $this->approval = $approval;

        // set the owning side of the relation if necessary
        if ($approval->getUser() !== $this) {
            $approval->setUser($this);
        }

        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(Publication $publication): self
    {
        $this->publication = $publication;

        // set the owning side of the relation if necessary
        if ($publication->getUser() !== $this) {
            $publication->setUser($this);
        }

        return $this;
    }


}