<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass="App\Repository\JobPostRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class JobPost
{

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     * @Assert\NotNull()
     * @Assert\Length(
     *     min=3,
     *     max=200,
     *     minMessage="Job title must be at minimum  3 characters long",
     *     maxMessage="Job Title must not be greater than 200 characters long"
     * )
     */
    private $title;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug = "job";


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     * @Assert\NotNull()
     * @Assert\Length(
     *     max=200,
     *     maxMessage="Company name must not be greater than 200 characters long"
     * )
     */
    private $company;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     * @Assert\NotNull()
     * @Assert\Length(
     *     max=200,
     *     maxMessage="Contract type must not be greater than 200 characters long"
     * )
     */
    private $contract;


    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     * @Assert\Type(
     *    type="integer",
     *    message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\Range(
     *     min=0,
     *     max= 10,
     *     minMessage="Experience must be at least {{ limit }}",
     *     maxMessage="Experience value must be at most {{ limit }}"
     * )
     *
     */
    private $experience;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max=200,
     *     maxMessage="Salary must not be greater than 200 characters long"
     * )
     */
    private $salary;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Type(
     *    type="string",
     *    message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $location;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Email()
     */
    private $email;


    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $description;


    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */


    private $filename;

    /**
     * @var File|null
     * @Assert\File(
     * maxSize="100k",
     * maxSizeMessage="Max size 100Ko.",
     * mimeTypes={"image/png", "image/jpeg", "image/jpg","image/gif", "image/svg+xml"},
     * mimeTypesMessage= "Authorized format: png, jpeg, jpg, gif, svg"
     * )
     * @Vich\UploadableField(mapping="company_logo", fileNameProperty="filename")
     */


    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;


    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="jobPosts")
     */
    private $username;

    /**
     * init slug
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     */

    public function initSlug()
    {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->title);
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getContract(): ?string
    {
        return $this->contract;
    }

    public function setContract(string $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(int $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(?string $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return JobPost
     */
    public function setFilename(?string $filename): JobPost
    {
        $this->filename = $filename;
        return $this;
    }


    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return JobPost
     * @throws
     */

    public function setImageFile(?File $imageFile): JobPost
    {
        $this->imageFile = $imageFile;

        if ($this->imageFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }

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

    public function getUsername(): ?User
    {
        return $this->username;
    }

    public function setUsername(?User $username): self
    {
        $this->username = $username;

        return $this;
    }
}
