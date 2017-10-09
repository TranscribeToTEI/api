<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Testator;

/**
 * Will
 *
 * @ORM\Table(name="will")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WillRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_will",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_will",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "patch",
 *      href = @Hateoas\Route(
 *          "patch_will",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "remove_will",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class Will
{
    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "id"})
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "parent"})
     * @Serializer\MaxDepth(1)
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Entity", mappedBy="will")
     * @ORM\JoinColumn(nullable=true)
     */
    private $entity;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank(message = "La côte ne peut pas être vide")
     *
     * @ORM\Column(name="callNumber", type="string", length=255)
     */
    private $callNumber;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\Url()
     *
     * @ORM\Column(name="minuteLink", type="text", nullable=true)
     */
    private $minuteLink;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank(message = "Le titre ne peut pas être vide")
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank(message = "La date de la minute ne peut pas être vide")
     *
     * @ORM\Column(name="minuteDate", type="string", length=255)
     */
    private $minuteDate;

    /**
     * The field is used to index dates in search
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="minuteYear", type="string", length=5)
     */
    private $minuteYear;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank(message = "La date d'écriture du testament ne peut pas être vide")
     *
     * @ORM\Column(name="willWritingDate", type="string", length=255)
     */
    private $willWritingDate;

    /**
     * The field is used to index dates in search
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="willWritingYear", type="string", length=5)
     */
    private $willWritingYear;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(3)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumn(nullable=true)
     */
    private $willWritingPlace;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "testator"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(4)
     *
     * @Assert\NotBlank(message = "Le champ testateur ne peut pas être vide")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Testator", inversedBy="wills", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $testator;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="pagePhysDescSupport", type="string", length=255, nullable=true)
     */
    private $pagePhysDescSupport;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="pagePhysDescHeight", type="string", length=255, nullable=true)
     */
    private $pagePhysDescHeight;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="pagePhysDescWidth", type="string", length=255, nullable=true)
     */
    private $pagePhysDescWidth;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="pagePhysDescHand", type="string", length=255, nullable=true)
     */
    private $pagePhysDescHand;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="envelopePhysDescSupport", type="string", length=255, nullable=true)
     */
    private $envelopePhysDescSupport;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="envelopePhysDescHeight", type="string", length=255, nullable=true)
     */
    private $envelopePhysDescHeight;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="envelopePhysDescWidth", type="string", length=255, nullable=true)
     */
    private $envelopePhysDescWidth;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="envelopePhysDescHand", type="string", length=255, nullable=true)
     */
    private $envelopePhysDescHand;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice({"AN", "AD78"})
     *
     * @ORM\Column(name="hostingOrganization", type="string", length=255, nullable=true)
     */
    private $hostingOrganization;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     *
     * @var string
     *
     * @ORM\Column(name="identificationUser", type="string", length=255, nullable=true)
     */
    private $identificationUser;

    /**
     * Use to add notes about the will
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Serializer\MaxDepth(1)
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $createUser;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     *
     * @var \Datetime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createDate", type="datetime", nullable=false)
     */
    protected $createDate;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Serializer\MaxDepth(1)
     * @Gedmo\Versioned
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $updateUser;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updateDate", type="datetime", nullable=false)
     */
    protected $updateDate;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Gedmo\Versioned
     *
     * @Assert\Type("string")
     *
     * @var string
     *
     * @ORM\Column(name="updateComment", type="string", length=255, nullable=true)
     */
    private $updateComment;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set callNumber
     *
     * @param string $callNumber
     *
     * @return Will
     */
    public function setCallNumber($callNumber)
    {
        $this->callNumber = $callNumber;

        return $this;
    }

    /**
     * Get callNumber
     *
     * @return string
     */
    public function getCallNumber()
    {
        return $this->callNumber;
    }

    /**
     * Set minuteLink
     *
     * @param string $minuteLink
     *
     * @return Will
     */
    public function setMinuteLink($minuteLink)
    {
        $this->minuteLink = $minuteLink;

        return $this;
    }

    /**
     * Get minuteLink
     *
     * @return string
     */
    public function getMinuteLink()
    {
        return $this->minuteLink;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Will
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set minuteDate
     *
     * @param string $minuteDate
     *
     * @return Will
     */
    public function setMinuteDate($minuteDate)
    {
        $this->minuteDate = $minuteDate;

        return $this;
    }

    /**
     * Get minuteDate
     *
     * @return string
     */
    public function getMinuteDate()
    {
        return $this->minuteDate;
    }

    /**
     * Set minuteYear
     *
     * @param string $minuteYear
     *
     * @return Will
     */
    public function setMinuteYear($minuteYear)
    {
        $this->minuteYear = $minuteYear;

        return $this;
    }

    /**
     * Get minuteYear
     *
     * @return string
     */
    public function getMinuteYear()
    {
        return $this->minuteYear;
    }

    /**
     * Set willWritingDate
     *
     * @param string $willWritingDate
     *
     * @return Will
     */
    public function setWillWritingDate($willWritingDate)
    {
        $this->willWritingDate = $willWritingDate;

        return $this;
    }

    /**
     * Get willWritingDate
     *
     * @return string
     */
    public function getWillWritingDate()
    {
        return $this->willWritingDate;
    }

    /**
     * Set willWritingYear
     *
     * @param string $willWritingYear
     *
     * @return Will
     */
    public function setWillWritingYear($willWritingYear)
    {
        $this->willWritingYear = $willWritingYear;

        return $this;
    }

    /**
     * Get willWritingYear
     *
     * @return string
     */
    public function getWillWritingYear()
    {
        return $this->willWritingYear;
    }

    /**
     * Set pagePhysDescSupport
     *
     * @param string $pagePhysDescSupport
     *
     * @return Will
     */
    public function setPagePhysDescSupport($pagePhysDescSupport)
    {
        $this->pagePhysDescSupport = $pagePhysDescSupport;

        return $this;
    }

    /**
     * Get pagePhysDescSupport
     *
     * @return string
     */
    public function getPagePhysDescSupport()
    {
        return $this->pagePhysDescSupport;
    }

    /**
     * Set pagePhysDescHeight
     *
     * @param string $pagePhysDescHeight
     *
     * @return Will
     */
    public function setPagePhysDescHeight($pagePhysDescHeight)
    {
        $this->pagePhysDescHeight = $pagePhysDescHeight;

        return $this;
    }

    /**
     * Get pagePhysDescHeight
     *
     * @return string
     */
    public function getPagePhysDescHeight()
    {
        return $this->pagePhysDescHeight;
    }

    /**
     * Set pagePhysDescWidth
     *
     * @param string $pagePhysDescWidth
     *
     * @return Will
     */
    public function setPagePhysDescWidth($pagePhysDescWidth)
    {
        $this->pagePhysDescWidth = $pagePhysDescWidth;

        return $this;
    }

    /**
     * Get pagePhysDescWidth
     *
     * @return string
     */
    public function getPagePhysDescWidth()
    {
        return $this->pagePhysDescWidth;
    }

    /**
     * Set pagePhysDescHand
     *
     * @param string $pagePhysDescHand
     *
     * @return Will
     */
    public function setPagePhysDescHand($pagePhysDescHand)
    {
        $this->pagePhysDescHand = $pagePhysDescHand;

        return $this;
    }

    /**
     * Get pagePhysDescHand
     *
     * @return string
     */
    public function getPagePhysDescHand()
    {
        return $this->pagePhysDescHand;
    }

    /**
     * Set envelopePhysDescSupport
     *
     * @param string $envelopePhysDescSupport
     *
     * @return Will
     */
    public function setenvelopePhysDescSupport($envelopePhysDescSupport)
    {
        $this->envelopePhysDescSupport = $envelopePhysDescSupport;

        return $this;
    }

    /**
     * Get envelopePhysDescSupport
     *
     * @return string
     */
    public function getenvelopePhysDescSupport()
    {
        return $this->envelopePhysDescSupport;
    }

    /**
     * Set envelopePhysDescHeight
     *
     * @param string $envelopePhysDescHeight
     *
     * @return Will
     */
    public function setenvelopePhysDescHeight($envelopePhysDescHeight)
    {
        $this->envelopePhysDescHeight = $envelopePhysDescHeight;

        return $this;
    }

    /**
     * Get envelopePhysDescHeight
     *
     * @return string
     */
    public function getenvelopePhysDescHeight()
    {
        return $this->envelopePhysDescHeight;
    }

    /**
     * Set envelopePhysDescWidth
     *
     * @param string $envelopePhysDescWidth
     *
     * @return Will
     */
    public function setenvelopePhysDescWidth($envelopePhysDescWidth)
    {
        $this->envelopePhysDescWidth = $envelopePhysDescWidth;

        return $this;
    }

    /**
     * Get envelopePhysDescWidth
     *
     * @return string
     */
    public function getenvelopePhysDescWidth()
    {
        return $this->envelopePhysDescWidth;
    }

    /**
     * Set envelopePhysDescHand
     *
     * @param string $envelopePhysDescHand
     *
     * @return Will
     */
    public function setenvelopePhysDescHand($envelopePhysDescHand)
    {
        $this->envelopePhysDescHand = $envelopePhysDescHand;

        return $this;
    }

    /**
     * Get envelopePhysDescHand
     *
     * @return string
     */
    public function getenvelopePhysDescHand()
    {
        return $this->envelopePhysDescHand;
    }

    /**
     * Set hostingOrganization
     *
     * @param string $hostingOrganization
     *
     * @return Will
     */
    public function setHostingOrganization($hostingOrganization)
    {
        $this->hostingOrganization = $hostingOrganization;

        return $this;
    }

    /**
     * Get hostingOrganization
     *
     * @return string
     */
    public function getHostingOrganization()
    {
        return $this->hostingOrganization;
    }

    /**
     * Set identificationUser
     *
     * @param string $identificationUser
     *
     * @return Will
     */
    public function setIdentificationUser($identificationUser)
    {
        $this->identificationUser = $identificationUser;

        return $this;
    }

    /**
     * Get identificationUser
     *
     * @return string
     */
    public function getIdentificationUser()
    {
        return $this->identificationUser;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Will
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Will
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set updateComment
     *
     * @param string $updateComment
     *
     * @return Will
     */
    public function setUpdateComment($updateComment)
    {
        $this->updateComment = $updateComment;

        return $this;
    }

    /**
     * Get updateComment
     *
     * @return string
     */
    public function getUpdateComment()
    {
        return $this->updateComment;
    }

    /**
     * Set entity
     *
     * @param \AppBundle\Entity\Entity $entity
     *
     * @return Will
     */
    public function setEntity(\AppBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return \AppBundle\Entity\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set willWritingPlace
     *
     * @param \AppBundle\Entity\Place $willWritingPlace
     *
     * @return Will
     */
    public function setWillWritingPlace(\AppBundle\Entity\Place $willWritingPlace = null)
    {
        $this->willWritingPlace = $willWritingPlace;

        return $this;
    }

    /**
     * Get willWritingPlace
     *
     * @return \AppBundle\Entity\Place
     */
    public function getWillWritingPlace()
    {
        return $this->willWritingPlace;
    }

    /**
     * Set testator
     *
     * @param \AppBundle\Entity\Testator $testator
     *
     * @return Will
     */
    public function setTestator(\AppBundle\Entity\Testator $testator)
    {
        $this->testator = $testator;

        return $this;
    }

    /**
     * Get testator
     *
     * @return \AppBundle\Entity\Testator
     */
    public function getTestator()
    {
        return $this->testator;
    }

    /**
     * Set createUser
     *
     * @param \UserBundle\Entity\User $createUser
     *
     * @return Will
     */
    public function setCreateUser(\UserBundle\Entity\User $createUser = null)
    {
        $this->createUser = $createUser;

        return $this;
    }

    /**
     * Get createUser
     *
     * @return \UserBundle\Entity\User
     */
    public function getCreateUser()
    {
        return $this->createUser;
    }

    /**
     * Set updateUser
     *
     * @param \UserBundle\Entity\User $updateUser
     *
     * @return Will
     */
    public function setUpdateUser(\UserBundle\Entity\User $updateUser = null)
    {
        $this->updateUser = $updateUser;

        return $this;
    }

    /**
     * Get updateUser
     *
     * @return \UserBundle\Entity\User
     */
    public function getUpdateUser()
    {
        return $this->updateUser;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Will
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
