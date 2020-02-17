<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Content
 *
 * @ORM\Table(name="content", indexes={@ORM\Index(name="fk_user_approve_id", columns={"user_appove_id"}), @ORM\Index(name="fk_user_submit_id", columns={"user_submit_id"}), @ORM\Index(name="fk_user_publish_id", columns={"user_publish_id"})})
 * @ORM\Entity
 */
class Content
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="blob", length=0, nullable=false)
     */
    private $file;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="submit_date", type="datetime", nullable=false)
     */
    private $submitDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approval_date", type="datetime", nullable=false)
     */
    private $approvalDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publication_date", type="datetime", nullable=false)
     */
    private $publicationDate;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_submit_id", referencedColumnName="id")
     * })
     */
    private $userSubmit;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_appove_id", referencedColumnName="id")
     * })
     */
    private $userAppove;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_publish_id", referencedColumnName="id")
     * })
     */
    private $userPublish;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getSubmitDate(): ?\DateTimeInterface
    {
        return $this->submitDate;
    }

    public function setSubmitDate(\DateTimeInterface $submitDate): self
    {
        $this->submitDate = $submitDate;

        return $this;
    }

    public function getApprovalDate(): ?\DateTimeInterface
    {
        return $this->approvalDate;
    }

    public function setApprovalDate(\DateTimeInterface $approvalDate): self
    {
        $this->approvalDate = $approvalDate;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getUserSubmit(): ?User
    {
        return $this->userSubmit;
    }

    public function setUserSubmit(?User $userSubmit): self
    {
        $this->userSubmit = $userSubmit;

        return $this;
    }

    public function getUserAppove(): ?User
    {
        return $this->userAppove;
    }

    public function setUserAppove(?User $userAppove): self
    {
        $this->userAppove = $userAppove;

        return $this;
    }

    public function getUserPublish(): ?User
    {
        return $this->userPublish;
    }

    public function setUserPublish(?User $userPublish): self
    {
        $this->userPublish = $userPublish;

        return $this;
    }


}
