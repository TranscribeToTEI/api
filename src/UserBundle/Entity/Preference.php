<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Preference
 *
 * @ORM\Table(name="preference")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\PreferenceRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_preference",
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
 *          "update_preference",
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
 *          "patch_preference",
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
 *          "remove_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class Preference
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
     * @Assert\Choice({"leftRead-centerHelp-rightImage", "leftRead-centerImage-rightHelp", "leftHelp-centerRead-rightImage", "leftHelp-centerImage-rightRead", "leftImage-centerHelp-rightRead", "leftImage-centerRead-rightHelp"})
     * @var string
     *
     * @ORM\Column(name="transcriptionDeskPosition", type="string", length=255)
     */
    private $transcriptionDeskPosition;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"todo", "inProgress", "done", "notInterested"})
     *
     * @var string
     *
     * @ORM\Column(name="tutorial_status", type="string", length=255)
     */
    private $tutorialStatus;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="tutorialProgress", type="integer", nullable=true)
     */
    private $tutorialProgress;


    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var bool
     *
     * @ORM\Column(name="smartTEI", type="boolean", nullable=true)
     */
    private $smartTEI;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var bool
     *
     * @ORM\Column(name="showComplexEntry", type="boolean", nullable=true)
     */
    private $showComplexEntry;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var bool
     *
     * @ORM\Column(name="creditActions", type="boolean", nullable=true)
     */
    private $creditActions;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\MaxDepth(2)
     */
    protected $user;


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
     * Set transcriptionDeskPosition
     *
     * @param string $transcriptionDeskPosition
     *
     * @return Preference
     */
    public function setTranscriptionDeskPosition($transcriptionDeskPosition)
    {
        $this->transcriptionDeskPosition = $transcriptionDeskPosition;

        return $this;
    }

    /**
     * Get transcriptionDeskPosition
     *
     * @return string
     */
    public function getTranscriptionDeskPosition()
    {
        return $this->transcriptionDeskPosition;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Preference
     */
    public function setUser(\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set tutorialStatus
     *
     * @param string $tutorialStatus
     *
     * @return Preference
     */
    public function setTutorialStatus($tutorialStatus)
    {
        $this->tutorialStatus = $tutorialStatus;

        return $this;
    }

    /**
     * Get tutorialStatus
     *
     * @return string
     */
    public function getTutorialStatus()
    {
        return $this->tutorialStatus;
    }

    /**
     * Set smartTEI
     *
     * @param boolean $smartTEI
     *
     * @return Preference
     */
    public function setSmartTEI($smartTEI)
    {
        $this->smartTEI = $smartTEI;

        return $this;
    }

    /**
     * Get smartTEI
     *
     * @return boolean
     */
    public function getSmartTEI()
    {
        return $this->smartTEI;
    }

    /**
     * Set showComplexEntry
     *
     * @param boolean $showComplexEntry
     *
     * @return Preference
     */
    public function setShowComplexEntry($showComplexEntry)
    {
        $this->showComplexEntry = $showComplexEntry;

        return $this;
    }

    /**
     * Get showComplexEntry
     *
     * @return boolean
     */
    public function getShowComplexEntry()
    {
        return $this->showComplexEntry;
    }

    /**
     * Set tutorialProgress
     *
     * @param integer $tutorialProgress
     *
     * @return Preference
     */
    public function setTutorialProgress($tutorialProgress)
    {
        $this->tutorialProgress = $tutorialProgress;

        return $this;
    }

    /**
     * Get tutorialProgress
     *
     * @return integer
     */
    public function getTutorialProgress()
    {
        return $this->tutorialProgress;
    }

    /**
     * Set creditActions
     *
     * @param boolean $creditActions
     *
     * @return Preference
     */
    public function setCreditActions($creditActions)
    {
        $this->creditActions = $creditActions;

        return $this;
    }

    /**
     * Get creditActions
     *
     * @return boolean
     */
    public function getCreditActions()
    {
        return $this->creditActions;
    }
}
