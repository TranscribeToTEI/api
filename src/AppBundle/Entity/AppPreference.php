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
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="creditsContent", type="integer", nullable=true, unique=true)
     */
    private $creditsContent;

    /**
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Assert\NotBlank()
     *
     * @var bool
     *
     * @ORM\Column(name="enableContact", type="boolean")
     */
    private $enableContact;

    /**
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
}
