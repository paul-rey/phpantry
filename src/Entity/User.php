<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"email"}, message="Email already taken")
 * @UniqueEntity(fields={"username"}, message="Username already taken")
 */
class User implements UserInterface
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message="Please type a valid email")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(min=6,minMessage="Your password must be at least {{ limit }} characters long")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter a username")
     * @Assert\Length(min=2,minMessage="Your username must be at least {{ limit }} characters long")
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\JobPost", mappedBy="username", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $jobPosts;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * User constructor.
     * @throws
     */
    public function __construct()
    {
        $this->jobPosts = new ArrayCollection();
        $this->token = bin2hex(random_bytes(10));
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');

    }

    // permet de récupérer username(email) via twig
    public function __toString()
    {
        return (string)$this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
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
        return (string)$this->password;
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

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|JobPost[]
     */
    public function getJobPosts(): Collection
    {
        return $this->jobPosts;
    }

    public function addJobPost(JobPost $jobPost): self
    {
        if (!$this->jobPosts->contains($jobPost)) {
            $this->jobPosts[] = $jobPost;
            $jobPost->setUsername($this);
        }

        return $this;
    }

    public function removeJobPost(JobPost $jobPost): self
    {
        if ($this->jobPosts->contains($jobPost)) {
            $this->jobPosts->removeElement($jobPost);
            // set the owning side to null (unless already changed)
            if ($jobPost->getUsername() === $this) {
                $jobPost->setUsername(null);
            }
        }

        return $this;
    }

    /**
     * init updatedat
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     * @throws
     */

    public function initUpdatedAt()
    {
        $this->setUpdatedAt(new \DateTime('now'));
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
