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
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="approval_date", type="datetime", nullable=true)
     */
    private $approvalDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publication_date", type="datetime", nullable=true)
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
    private $userApprove;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_publish_id", referencedColumnName="id")
     * })
     */
    private $userPublish;

    /**
     * @ORM\Column(type="smallint", options={"comment":"0:submitted, 1: accepted, 2: refused, 3:published"})
     */
    private $statut;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Approval", mappedBy="content", cascade={"persist", "remove"})
     */
    private $approval;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->statut = 0;
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
        return $this->userApprove;
    }

    public function setUserAppove(?User $userApprove): self
    {
        $this->userApprove = $userApprove;

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

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getApproval(): ?Approval
    {
        return $this->approval;
    }

    public function setApproval(Approval $approval): self
    {
        $this->approval = $approval;

        // set the owning side of the relation if necessary
        if ($approval->getContent() !== $this) {
            $approval->setContent($this);
        }

        return $this;
    }
}
