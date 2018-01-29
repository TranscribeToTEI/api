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
     * @Serializer\Groups({"full", "id", "search"})
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The entity aggregating the will
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "parent", "iiif"})
     * @Serializer\MaxDepth(1)
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Entity", mappedBy="will")
     * @ORM\JoinColumn(nullable=true)
     */
    private $entity;

    /**
     * The number at the archives of the will
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "listEntities", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank(message = "La côte ne peut pas être vide")
     *
     * @ORM\Column(name="callNumber", type="string", length=255, nullable=false)
     */
    private $callNumber;

    /**
     * The notary number for the collection hosting the will
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "listEntities", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank(message = "Le numéro d'étude notariale ne peut pas être vide")
     *
     * @ORM\Column(name="notaryNumber", type="string", length=255, nullable=false)
     */
    private $notaryNumber;

    /**
     * Identification number of the lawyer who received the will
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "listEntities", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="crpcenNumber", type="string", length=255, nullable=true)
     */
    private $crpcenNumber;

    /**
     * Title computed for the will, this is an automatic aggregation
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "pageEntity", "pageEdition", "pageTranscript", "taxonomyView", "listEntities", "adminEntity", "adminValidation"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank(message = "Le titre ne peut pas être vide")
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * Link of the minute at the archives
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\Url()
     *
     * @ORM\Column(name="minuteLink", type="text", nullable=true)
     */
    private $minuteLink;

    /**
     * Full date of the minute (string)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "listEntities", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank(message = "La date de la minute ne peut pas être vide")
     *
     * @ORM\Column(name="minuteDateString", type="text", nullable=false)
     */
    private $minuteDateString;

    /**
     * Normalized date of the minute
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\Date()
     * @Assert\NotNull(message = "La date normalisée de la minute ne peut pas être vide")
     *
     * @ORM\Column(name="minuteDateNormalized", type="date", nullable=false)
     */
    private $minuteDateNormalized;

    /**
     * If the date of the minute is an interval, fill here the end date of the interval
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\Date()
     *
     * @ORM\Column(name="minuteDateEndNormalized", type="date", nullable=true)
     */
    private $minuteDateEndNormalized;

    /**
     * The year of the minute, used for index
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "adminEntity"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="minuteYear", type="string", length=5, nullable=false)
     */
    private $minuteYear;

    /**
     * Full date of the will writing date (string)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "listEntities", "adminEntity", "taxonomyView"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank(message = "La date d'écriture du testament ne peut pas être vide")
     *
     * @ORM\Column(name="willWritingDateString", type="text", nullable=false)
     */
    private $willWritingDateString;

    /**
     * Normalized will writing date
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\Date(message = "test")
     * @Assert\NotNull(message = "La date d'écriture normalisée du testament ne peut pas être vide")
     *
     * @ORM\Column(name="willWritingDateNormalized", type="date", nullable=false)
     */
    private $willWritingDateNormalized;

    /**
     * If the will writing date is an interval, fill here the end date
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\Date()
     *
     * @ORM\Column(name="willWritingDateEndNormalized", type="date", nullable=true)
     */
    private $willWritingDateEndNormalized;

    /**
     * The year of the will writing date, used for index
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "listEntities", "adminEntity"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="willWritingYear", type="string", length=5)
     */
    private $willWritingYear;

    /**
     * Normalized will writing place
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(3)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumn(nullable=true)
     */
    private $willWritingPlaceNormalized;

    /**
     * Full will writing place (string)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "listEntities", "adminEntity"})
     *
     * @var string
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="willWritingPlaceString", type="text", nullable=true)
     */
    private $willWritingPlaceString;

    /**
     * The testator who wrote the will
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "testator", "search", "infoWill", "listEntities", "adminEntity"})
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
     * Support of the pages of the will
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="pagePhysDescSupport", type="string", length=255, nullable=true)
     */
    private $pagePhysDescSupport;

    /**
     * Height of the pages of the will (in centimeters)
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="pagePhysDescHeight", type="string", length=255, nullable=true)
     */
    private $pagePhysDescHeight;

    /**
     * Width of the pages of the will (in centimeters)
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="pagePhysDescWidth", type="string", length=255, nullable=true)
     */
    private $pagePhysDescWidth;

    /**
     * Type of hand writing for the page of the will
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="pagePhysDescHand", type="string", length=255, nullable=true)
     */
    private $pagePhysDescHand;

    /**
     * Number of pages of the will (in centimeters)
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "infoWill", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="pagePhysDescNumber", type="string", length=255, nullable=true)
     */
    private $pagePhysDescNumber;

    /**
     * Type of support of the will's envelop
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="envelopePhysDescSupport", type="string", length=255, nullable=true)
     */
    private $envelopePhysDescSupport;

    /**
     * Height of the will's envelop
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="envelopePhysDescHeight", type="string", length=255, nullable=true)
     */
    private $envelopePhysDescHeight;

    /**
     * Width of the will's envelop
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="envelopePhysDescWidth", type="string", length=255, nullable=true)
     */
    private $envelopePhysDescWidth;

    /**
     * Type of hand writing for the will's envelop
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="envelopePhysDescHand", type="string", length=255, nullable=true)
     */
    private $envelopePhysDescHand;

    /**
     * Type of support for the will's codicil
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="codicilPhysDescSupport", type="string", length=255, nullable=true)
     */
    private $codicilPhysDescSupport;

    /**
     * Height of the will's codicil
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="codicilPhysDescHeight", type="string", length=255, nullable=true)
     */
    private $codicilPhysDescHeight;

    /**
     * Width of the will's codicil
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="codicilPhysDescWidth", type="string", length=255, nullable=true)
     */
    private $codicilPhysDescWidth;

    /**
     * Type of hand writing of the will's codicil
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="codicilPhysDescHand", type="string", length=255, nullable=true)
     */
    private $codicilPhysDescHand;

    /**
     * Number of pages of the codicil
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="codicilPhysDescNumber", type="string", length=255, nullable=true)
     */
    private $codicilPhysDescNumber;

    /**
     * The type of will (related to WillType)
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "listEntities", "adminEntity"})
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WillType")
     * @ORM\JoinColumn(nullable=true)
     */
    private $willType;

    /**
     * The hosting organization of the will
     * 
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "pageEdition", "pageEntity", "pageTranscript", "iiif", "pageInstitution", "listEntities", "adminEntity"})
     * @Serializer\MaxDepth(3)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\HostingOrganization")
     * @ORM\JoinColumn(nullable=true)
     */
    private $hostingOrganization;

    /**
     * Users who contribute to identify the will and its metadata before the transcription process
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata", "adminEntity"})
     *
     * @var string
     *
     * @ORM\Column(name="identificationUsers", type="text", nullable=true)
     */
    private $identificationUsers;

    /**
     * Use to add notes about the will
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * Use to additional comments notes about the will
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="additionalComments", type="text", nullable=true)
     */
    private $additionalComments;

    /**
     * Is the current version an official version of the project team?
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="isOfficialVersion", type="boolean", options={"default" : false})
     */
    private $isOfficialVersion;

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
     * @ORM\Column(name="updateComment", type="text", length=255, nullable=true)
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
     * Set notaryNumber
     *
     * @param string $notaryNumber
     *
     * @return Will
     */
    public function setNotaryNumber($notaryNumber)
    {
        $this->notaryNumber = $notaryNumber;

        return $this;
    }

    /**
     * Get notaryNumber
     *
     * @return string
     */
    public function getNotaryNumber()
    {
        return $this->notaryNumber;
    }

    /**
     * Set crpcenNumber
     *
     * @param string $crpcenNumber
     *
     * @return Will
     */
    public function setCrpcenNumber($crpcenNumber)
    {
        $this->crpcenNumber = $crpcenNumber;

        return $this;
    }

    /**
     * Get crpcenNumber
     *
     * @return string
     */
    public function getCrpcenNumber()
    {
        return $this->crpcenNumber;
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
     * Set minuteDateString
     *
     * @param string $minuteDateString
     *
     * @return Will
     */
    public function setMinuteDateString($minuteDateString)
    {
        $this->minuteDateString = $minuteDateString;

        return $this;
    }

    /**
     * Get minuteDateString
     *
     * @return string
     */
    public function getMinuteDateString()
    {
        return $this->minuteDateString;
    }

    /**
     * Set minuteDateNormalized
     *
     * @param \DateTime $minuteDateNormalized
     *
     * @return Will
     */
    public function setMinuteDateNormalized($minuteDateNormalized)
    {
        $this->minuteDateNormalized = $minuteDateNormalized;

        return $this;
    }

    /**
     * Get minuteDateNormalized
     *
     * @return \DateTime
     */
    public function getMinuteDateNormalized()
    {
        return $this->minuteDateNormalized;
    }

    /**
     * Set minuteDateEndNormalized
     *
     * @param \DateTime $minuteDateEndNormalized
     *
     * @return Will
     */
    public function setMinuteDateEndNormalized($minuteDateEndNormalized)
    {
        $this->minuteDateEndNormalized = $minuteDateEndNormalized;

        return $this;
    }

    /**
     * Get minuteDateEndNormalized
     *
     * @return \DateTime
     */
    public function getMinuteDateEndNormalized()
    {
        return $this->minuteDateEndNormalized;
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
     * Set willWritingDateString
     *
     * @param string $willWritingDateString
     *
     * @return Will
     */
    public function setWillWritingDateString($willWritingDateString)
    {
        $this->willWritingDateString = $willWritingDateString;

        return $this;
    }

    /**
     * Get willWritingDateString
     *
     * @return string
     */
    public function getWillWritingDateString()
    {
        return $this->willWritingDateString;
    }

    /**
     * Set willWritingDateNormalized
     *
     * @param \DateTime $willWritingDateNormalized
     *
     * @return Will
     */
    public function setWillWritingDateNormalized($willWritingDateNormalized)
    {
        $this->willWritingDateNormalized = $willWritingDateNormalized;

        return $this;
    }

    /**
     * Get willWritingDateNormalized
     *
     * @return \DateTime
     */
    public function getWillWritingDateNormalized()
    {
        return $this->willWritingDateNormalized;
    }

    /**
     * Set willWritingDateEndNormalized
     *
     * @param \DateTime $willWritingDateEndNormalized
     *
     * @return Will
     */
    public function setWillWritingDateEndNormalized($willWritingDateEndNormalized)
    {
        $this->willWritingDateEndNormalized = $willWritingDateEndNormalized;

        return $this;
    }

    /**
     * Get willWritingDateEndNormalized
     *
     * @return \DateTime
     */
    public function getWillWritingDateEndNormalized()
    {
        return $this->willWritingDateEndNormalized;
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
     * Set willWritingPlaceString
     *
     * @param string $willWritingPlaceString
     *
     * @return Will
     */
    public function setWillWritingPlaceString($willWritingPlaceString)
    {
        $this->willWritingPlaceString = $willWritingPlaceString;

        return $this;
    }

    /**
     * Get willWritingPlaceString
     *
     * @return string
     */
    public function getWillWritingPlaceString()
    {
        return $this->willWritingPlaceString;
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
     * Set pagePhysDescNumber
     *
     * @param string $pagePhysDescNumber
     *
     * @return Will
     */
    public function setPagePhysDescNumber($pagePhysDescNumber)
    {
        $this->pagePhysDescNumber = $pagePhysDescNumber;

        return $this;
    }

    /**
     * Get pagePhysDescNumber
     *
     * @return string
     */
    public function getPagePhysDescNumber()
    {
        return $this->pagePhysDescNumber;
    }

    /**
     * Set envelopePhysDescSupport
     *
     * @param string $envelopePhysDescSupport
     *
     * @return Will
     */
    public function setEnvelopePhysDescSupport($envelopePhysDescSupport)
    {
        $this->envelopePhysDescSupport = $envelopePhysDescSupport;

        return $this;
    }

    /**
     * Get envelopePhysDescSupport
     *
     * @return string
     */
    public function getEnvelopePhysDescSupport()
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
    public function setEnvelopePhysDescHeight($envelopePhysDescHeight)
    {
        $this->envelopePhysDescHeight = $envelopePhysDescHeight;

        return $this;
    }

    /**
     * Get envelopePhysDescHeight
     *
     * @return string
     */
    public function getEnvelopePhysDescHeight()
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
    public function setEnvelopePhysDescWidth($envelopePhysDescWidth)
    {
        $this->envelopePhysDescWidth = $envelopePhysDescWidth;

        return $this;
    }

    /**
     * Get envelopePhysDescWidth
     *
     * @return string
     */
    public function getEnvelopePhysDescWidth()
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
    public function setEnvelopePhysDescHand($envelopePhysDescHand)
    {
        $this->envelopePhysDescHand = $envelopePhysDescHand;

        return $this;
    }

    /**
     * Get envelopePhysDescHand
     *
     * @return string
     */
    public function getEnvelopePhysDescHand()
    {
        return $this->envelopePhysDescHand;
    }

    /**
     * Set codicilPhysDescSupport
     *
     * @param string $codicilPhysDescSupport
     *
     * @return Will
     */
    public function setCodicilPhysDescSupport($codicilPhysDescSupport)
    {
        $this->codicilPhysDescSupport = $codicilPhysDescSupport;

        return $this;
    }

    /**
     * Get codicilPhysDescSupport
     *
     * @return string
     */
    public function getCodicilPhysDescSupport()
    {
        return $this->codicilPhysDescSupport;
    }

    /**
     * Set codicilPhysDescHeight
     *
     * @param string $codicilPhysDescHeight
     *
     * @return Will
     */
    public function setCodicilPhysDescHeight($codicilPhysDescHeight)
    {
        $this->codicilPhysDescHeight = $codicilPhysDescHeight;

        return $this;
    }

    /**
     * Get codicilPhysDescHeight
     *
     * @return string
     */
    public function getCodicilPhysDescHeight()
    {
        return $this->codicilPhysDescHeight;
    }

    /**
     * Set codicilPhysDescWidth
     *
     * @param string $codicilPhysDescWidth
     *
     * @return Will
     */
    public function setCodicilPhysDescWidth($codicilPhysDescWidth)
    {
        $this->codicilPhysDescWidth = $codicilPhysDescWidth;

        return $this;
    }

    /**
     * Get codicilPhysDescWidth
     *
     * @return string
     */
    public function getCodicilPhysDescWidth()
    {
        return $this->codicilPhysDescWidth;
    }

    /**
     * Set codicilPhysDescHand
     *
     * @param string $codicilPhysDescHand
     *
     * @return Will
     */
    public function setCodicilPhysDescHand($codicilPhysDescHand)
    {
        $this->codicilPhysDescHand = $codicilPhysDescHand;

        return $this;
    }

    /**
     * Get codicilPhysDescHand
     *
     * @return string
     */
    public function getCodicilPhysDescHand()
    {
        return $this->codicilPhysDescHand;
    }

    /**
     * Set codicilPhysDescNumber
     *
     * @param string $codicilPhysDescNumber
     *
     * @return Will
     */
    public function setCodicilPhysDescNumber($codicilPhysDescNumber)
    {
        $this->codicilPhysDescNumber = $codicilPhysDescNumber;

        return $this;
    }

    /**
     * Get codicilPhysDescNumber
     *
     * @return string
     */
    public function getCodicilPhysDescNumber()
    {
        return $this->codicilPhysDescNumber;
    }

    /**
     * Set identificationUsers
     *
     * @param string $identificationUsers
     *
     * @return Will
     */
    public function setidentificationUsers($identificationUsers)
    {
        $this->identificationUsers = $identificationUsers;

        return $this;
    }

    /**
     * Get identificationUsers
     *
     * @return string
     */
    public function getidentificationUsers()
    {
        return $this->identificationUsers;
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

    /**
     * Set additionalComments
     *
     * @param string $additionalComments
     *
     * @return Will
     */
    public function setAdditionalComments($additionalComments)
    {
        $this->additionalComments = $additionalComments;

        return $this;
    }

    /**
     * Get additionalComments
     *
     * @return string
     */
    public function getAdditionalComments()
    {
        return $this->additionalComments;
    }

    /**
     * Set isOfficialVersion
     *
     * @param boolean $isOfficialVersion
     *
     * @return Will
     */
    public function setIsOfficialVersion($isOfficialVersion)
    {
        $this->isOfficialVersion = $isOfficialVersion;

        return $this;
    }

    /**
     * Get isOfficialVersion
     *
     * @return boolean
     */
    public function getIsOfficialVersion()
    {
        return $this->isOfficialVersion;
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
     * Set willWritingPlaceNormalized
     *
     * @param \AppBundle\Entity\Place $willWritingPlaceNormalized
     *
     * @return Will
     */
    public function setWillWritingPlaceNormalized(\AppBundle\Entity\Place $willWritingPlaceNormalized = null)
    {
        $this->willWritingPlaceNormalized = $willWritingPlaceNormalized;

        return $this;
    }

    /**
     * Get willWritingPlaceNormalized
     *
     * @return \AppBundle\Entity\Place
     */
    public function getWillWritingPlaceNormalized()
    {
        return $this->willWritingPlaceNormalized;
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
     * Set willType
     *
     * @param \AppBundle\Entity\WillType $willType
     *
     * @return Will
     */
    public function setWillType(\AppBundle\Entity\WillType $willType = null)
    {
        $this->willType = $willType;

        return $this;
    }

    /**
     * Get willType
     *
     * @return \AppBundle\Entity\WillType
     */
    public function getWillType()
    {
        return $this->willType;
    }

    /**
     * Set hostingOrganization
     *
     * @param \AppBundle\Entity\HostingOrganization $hostingOrganization
     *
     * @return Will
     */
    public function setHostingOrganization(\AppBundle\Entity\HostingOrganization $hostingOrganization = null)
    {
        $this->hostingOrganization = $hostingOrganization;

        return $this;
    }

    /**
     * Get hostingOrganization
     *
     * @return \AppBundle\Entity\HostingOrganization
     */
    public function getHostingOrganization()
    {
        return $this->hostingOrganization;
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
}
