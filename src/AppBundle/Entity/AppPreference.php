<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * AppPreference
 *
 * @ORM\Table(name="app_preference")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppPreferenceRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_app_preference",
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
 *          "update_app_preference",
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
 *          "patch_app_preference",
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
 *          "remove_app_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class AppPreference
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
     * @Serializer\Groups({"full", "content"})
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @var string
     *
     * @ORM\Column(name="projectTitle", type="string", length=255, unique=true)
     */
    private $projectTitle;

    /**
     * Refers to the help homepage in the navbar of the project
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="helpHomeContent", type="integer", nullable=true, unique=true)
     */
    private $helpHomeContent;

    /**
     * Refers to the help homepage in the transcription tool of the project
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="helpInsideHomeContent", type="integer", nullable=true, unique=true)
     */
    private $helpInsideHomeContent;

    /**
     * Refers to the discover page in the navbar of the project
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="discoverHomeContent", type="integer", nullable=true, unique=true)
     */
    private $discoverHomeContent;

    /**
     * Refers to the about page in the footer of the project
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="aboutContent", type="integer", nullable=true, unique=true)
     */
    private $aboutContent;

    /**
     * Refers to the legal notices page in the footer of the project
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="legalNoticesContent", type="integer", nullable=true, unique=true)
     */
    private $legalNoticesContent;

    /**
     * Refers to the credits page in the footer of the project
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="creditsContent", type="integer", nullable=true, unique=true)
     */
    private $creditsContent;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Assert\Type("string")
     * @var string
     *
     * @ORM\Column(name="facebookPageId", type="string", length=255, unique=true, nullable=true)
     */
    private $facebookPageId;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Assert\Type("string")
     * @var string
     *
     * @ORM\Column(name="twitterId", type="string", length=255, unique=true, nullable=true)
     */
    private $twitterId;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var bool
     *
     * @ORM\Column(name="enableContact", type="boolean", nullable=true)
     */
    private $enableContact;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Assert\Email()
     * @var string
     *
     * @ORM\Column(name="systemEmail", type="string", length=255, unique=true, nullable=true)
     */
    private $systemEmail;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Assert\Email()
     * @var string
     *
     * @ORM\Column(name="contactEmail", type="string", length=255, unique=true, nullable=true)
     */
    private $contactEmail;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var bool
     *
     * @ORM\Column(name="enableRegister", type="boolean", nullable=true)
     */
    private $enableRegister;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice({"selfAuthorization", "controlledAuthorization", "free", "forbidden"})
     *
     * @ORM\Column(name="taxonomyEditAccess", type="string", length=255, nullable=false)
     */
    private $taxonomyEditAccess;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var bool
     *
     * @ORM\Column(name="transcriptEditAccess", type="boolean", nullable=true)
     */
    private $transcriptEditAccess;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="infoContentEditTaxonomy", type="text", nullable=true)
     */
    private $infoContentEditTaxonomy;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="taxonomyAccessProposal", type="text", nullable=true)
     */
    private $taxonomyAccessProposal;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="taxonomyAskQuestion", type="text", nullable=true)
     */
    private $taxonomyAskQuestion;


    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="infoContact", type="text", nullable=true)
     */
    private $infoContact;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(1)
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
     * Set helpHomeContent
     *
     * @param integer $helpHomeContent
     *
     * @return AppPreference
     */
    public function setHelpHomeContent($helpHomeContent)
    {
        $this->helpHomeContent = $helpHomeContent;

        return $this;
    }

    /**
     * Get helpHomeContent
     *
     * @return int
     */
    public function getHelpHomeContent()
    {
        return $this->helpHomeContent;
    }

    /**
     * Set discoverHomeContent
     *
     * @param integer $discoverHomeContent
     *
     * @return AppPreference
     */
    public function setDiscoverHomeContent($discoverHomeContent)
    {
        $this->discoverHomeContent = $discoverHomeContent;

        return $this;
    }

    /**
     * Get discoverHomeContent
     *
     * @return int
     */
    public function getDiscoverHomeContent()
    {
        return $this->discoverHomeContent;
    }

    /**
     * Set projectTitle
     *
     * @param string $projectTitle
     *
     * @return AppPreference
     */
    public function setProjectTitle($projectTitle)
    {
        $this->projectTitle = $projectTitle;

        return $this;
    }

    /**
     * Get projectTitle
     *
     * @return string
     */
    public function getProjectTitle()
    {
        return $this->projectTitle;
    }

    /**
     * Set helpInsideHomeContent
     *
     * @param integer $helpInsideHomeContent
     *
     * @return AppPreference
     */
    public function setHelpInsideHomeContent($helpInsideHomeContent)
    {
        $this->helpInsideHomeContent = $helpInsideHomeContent;

        return $this;
    }

    /**
     * Get helpInsideHomeContent
     *
     * @return integer
     */
    public function getHelpInsideHomeContent()
    {
        return $this->helpInsideHomeContent;
    }

    /**
     * Set aboutContent
     *
     * @param integer $aboutContent
     *
     * @return AppPreference
     */
    public function setAboutContent($aboutContent)
    {
        $this->aboutContent = $aboutContent;

        return $this;
    }

    /**
     * Get aboutContent
     *
     * @return integer
     */
    public function getAboutContent()
    {
        return $this->aboutContent;
    }

    /**
     * Set legalNoticesContent
     *
     * @param integer $legalNoticesContent
     *
     * @return AppPreference
     */
    public function setLegalNoticesContent($legalNoticesContent)
    {
        $this->legalNoticesContent = $legalNoticesContent;

        return $this;
    }

    /**
     * Get legalNoticesContent
     *
     * @return integer
     */
    public function getLegalNoticesContent()
    {
        return $this->legalNoticesContent;
    }

    /**
     * Set creditsContent
     *
     * @param integer $creditsContent
     *
     * @return AppPreference
     */
    public function setCreditsContent($creditsContent)
    {
        $this->creditsContent = $creditsContent;

        return $this;
    }

    /**
     * Get creditsContent
     *
     * @return integer
     */
    public function getCreditsContent()
    {
        return $this->creditsContent;
    }

    /**
     * Set facebookPageId
     *
     * @param string $facebookPageId
     *
     * @return AppPreference
     */
    public function setFacebookPageId($facebookPageId)
    {
        $this->facebookPageId = $facebookPageId;

        return $this;
    }

    /**
     * Get facebookPageId
     *
     * @return string
     */
    public function getFacebookPageId()
    {
        return $this->facebookPageId;
    }

    /**
     * Set twitterId
     *
     * @param string $twitterId
     *
     * @return AppPreference
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    /**
     * Get twitterId
     *
     * @return string
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * Set enableContact
     *
     * @param boolean $enableContact
     *
     * @return AppPreference
     */
    public function setEnableContact($enableContact)
    {
        $this->enableContact = $enableContact;

        return $this;
    }

    /**
     * Get enableContact
     *
     * @return boolean
     */
    public function getEnableContact()
    {
        return $this->enableContact;
    }

    /**
     * Set systemEmail
     *
     * @param string $systemEmail
     *
     * @return AppPreference
     */
    public function setSystemEmail($systemEmail)
    {
        $this->systemEmail = $systemEmail;

        return $this;
    }

    /**
     * Get systemEmail
     *
     * @return string
     */
    public function getSystemEmail()
    {
        return $this->systemEmail;
    }

    /**
     * Set contactEmail
     *
     * @param string $contactEmail
     *
     * @return AppPreference
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * Get contactEmail
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Set enableRegister
     *
     * @param boolean $enableRegister
     *
     * @return AppPreference
     */
    public function setEnableRegister($enableRegister)
    {
        $this->enableRegister = $enableRegister;

        return $this;
    }

    /**
     * Get enableRegister
     *
     * @return boolean
     */
    public function getEnableRegister()
    {
        return $this->enableRegister;
    }

    /**
     * Set taxonomyEditAccess
     *
     * @param string $taxonomyEditAccess
     *
     * @return AppPreference
     */
    public function setTaxonomyEditAccess($taxonomyEditAccess)
    {
        $this->taxonomyEditAccess = $taxonomyEditAccess;

        return $this;
    }

    /**
     * Get taxonomyEditAccess
     *
     * @return string
     */
    public function getTaxonomyEditAccess()
    {
        return $this->taxonomyEditAccess;
    }

    /**
     * Set transcriptEditAccess
     *
     * @param boolean $transcriptEditAccess
     *
     * @return AppPreference
     */
    public function setTranscriptEditAccess($transcriptEditAccess)
    {
        $this->transcriptEditAccess = $transcriptEditAccess;

        return $this;
    }

    /**
     * Get transcriptEditAccess
     *
     * @return boolean
     */
    public function getTranscriptEditAccess()
    {
        return $this->transcriptEditAccess;
    }

    /**
     * Set infoContentEditTaxonomy
     *
     * @param string $infoContentEditTaxonomy
     *
     * @return AppPreference
     */
    public function setInfoContentEditTaxonomy($infoContentEditTaxonomy)
    {
        $this->infoContentEditTaxonomy = $infoContentEditTaxonomy;

        return $this;
    }

    /**
     * Get infoContentEditTaxonomy
     *
     * @return string
     */
    public function getInfoContentEditTaxonomy()
    {
        return $this->infoContentEditTaxonomy;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return AppPreference
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
     * @return AppPreference
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
     * Set updateUser
     *
     * @param \UserBundle\Entity\User $updateUser
     *
     * @return AppPreference
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
     * Set infoContact
     *
     * @param string $infoContact
     *
     * @return AppPreference
     */
    public function setInfoContact($infoContact)
    {
        $this->infoContact = $infoContact;

        return $this;
    }

    /**
     * Get infoContact
     *
     * @return string
     */
    public function getInfoContact()
    {
        return $this->infoContact;
    }

    /**
     * Set taxonomyAskQuestion
     *
     * @param string $taxonomyAskQuestion
     *
     * @return AppPreference
     */
    public function setTaxonomyAskQuestion($taxonomyAskQuestion)
    {
        $this->taxonomyAskQuestion = $taxonomyAskQuestion;

        return $this;
    }

    /**
     * Get taxonomyAskQuestion
     *
     * @return string
     */
    public function getTaxonomyAskQuestion()
    {
        return $this->taxonomyAskQuestion;
    }

    /**
     * Set taxonomyAccessProposal
     *
     * @param string $taxonomyAccessProposal
     *
     * @return AppPreference
     */
    public function setTaxonomyAccessProposal($taxonomyAccessProposal)
    {
        $this->taxonomyAccessProposal = $taxonomyAccessProposal;

        return $this;
    }

    /**
     * Get taxonomyAccessProposal
     *
     * @return string
     */
    public function getTaxonomyAccessProposal()
    {
        return $this->taxonomyAccessProposal;
    }
}
