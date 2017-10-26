<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * TrainingContent
 *
 * @ORM\Table(name="training_content")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrainingContentRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_training_content",
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
 *          "update_training_content",
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
 *          "patch_training_content",
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
 *          "remove_training_content",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class TrainingContent
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
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @var string
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
     * @Assert\NotBlank()
     *
     * @var string
     *
     * @ORM\Column(name="internalGoal", type="text")
     */
    private $internalGoal;


    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     *
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"presentation", "exercise"})
     *
     * @var string
     *
     * @ORM\Column(name="pageType", type="string", length=255)
     */
    private $pageType;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"draft", "public", "notIndexed"})
     *
     * @var string
     *
     * @ORM\Column(name="pageStatus", type="string", length=255)
     */
    private $pageStatus;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="illustration", type="text", nullable=true)
     */
    private $illustration;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="videoContainer", type="text", nullable=true)
     */
    private $videoContainer;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="orderInTraining", type="integer", nullable=true)
     */
    private $orderInTraining;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $editorialResponsibility;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="exerciseHeader", type="text", nullable=true)
     */
    private $exerciseHeader;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="exerciseImageToTranscribe", type="text", nullable=true)
     */
    private $exerciseImageToTranscribe;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsSmartTEI", type="boolean", nullable=true)
     */
    private $exerciseIsSmartTEI;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsAttributesManagement", type="boolean", nullable=true)
     */
    private $exerciseIsAttributesManagement;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var array
     *
     * @ORM\Column(name="exerciseTagsList", type="array", nullable=true)
     */
    private $exerciseTagsList;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsLiveRender", type="boolean", nullable=true)
     */
    private $exerciseIsLiveRender;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsHelp", type="boolean", nullable=true)
     */
    private $exerciseIsHelp;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsDocumentation", type="boolean", nullable=true)
     */
    private $exerciseIsDocumentation;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsTaxonomy", type="boolean", nullable=true)
     */
    private $exerciseIsTaxonomy;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsBibliography", type="boolean", nullable=true)
     */
    private $exerciseIsBibliography;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsNotes", type="boolean", nullable=true)
     */
    private $exerciseIsNotes;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsVersioning", type="boolean", nullable=true)
     */
    private $exerciseIsVersioning;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="exerciseIsComplexFields", type="boolean", nullable=true)
     */
    private $exerciseIsComplexFields;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="exerciseCorrectionTranscript", type="text", nullable=true)
     */
    private $exerciseCorrectionTranscript;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="exerciseCorrectionErrorsToAvoid", type="text", nullable=true)
     */
    private $exerciseCorrectionErrorsToAvoid;

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
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createDate", type="datetime", nullable=false)
     */
    protected $createDate;

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
     * @ORM\Column(name="updateComment", type="string", length=255, nullable=false)
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
     * Set
     *
     * @param string $field
     * @param string $value
     *
     * @return TrainingContent
     */
    public function set($field, $value)
    {
        $this->{$field} = $value;

        return $this;
    }


    /**
     * Set title
     *
     * @param string $title
     *
     * @return TrainingContent
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
     * Set content
     *
     * @param string $content
     *
     * @return TrainingContent
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set pageType
     *
     * @param string $pageType
     *
     * @return TrainingContent
     */
    public function setPageType($pageType)
    {
        $this->pageType = $pageType;

        return $this;
    }

    /**
     * Get pageType
     *
     * @return string
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * Set pageStatus
     *
     * @param string $pageStatus
     *
     * @return TrainingContent
     */
    public function setPageStatus($pageStatus)
    {
        $this->pageStatus = $pageStatus;

        return $this;
    }

    /**
     * Get pageStatus
     *
     * @return string
     */
    public function getPageStatus()
    {
        return $this->pageStatus;
    }

    /**
     * Set illustration
     *
     * @param string $illustration
     *
     * @return TrainingContent
     */
    public function setIllustration($illustration)
    {
        $this->illustration = $illustration;

        return $this;
    }

    /**
     * Get illustration
     *
     * @return string
     */
    public function getIllustration()
    {
        return $this->illustration;
    }

    /**
     * Set orderInTraining
     *
     * @param integer $orderInTraining
     *
     * @return TrainingContent
     */
    public function setOrderInTraining($orderInTraining)
    {
        $this->orderInTraining = $orderInTraining;

        return $this;
    }

    /**
     * Get orderInTraining
     *
     * @return integer
     */
    public function getOrderInTraining()
    {
        return $this->orderInTraining;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return TrainingContent
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
     * @return TrainingContent
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
     * @return TrainingContent
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
     * Set createUser
     *
     * @param \UserBundle\Entity\User $createUser
     *
     * @return TrainingContent
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
     * @return TrainingContent
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
     * Set videoContainer
     *
     * @param string $videoContainer
     *
     * @return TrainingContent
     */
    public function setVideoContainer($videoContainer)
    {
        $this->videoContainer = $videoContainer;

        return $this;
    }

    /**
     * Get videoContainer
     *
     * @return string
     */
    public function getVideoContainer()
    {
        return $this->videoContainer;
    }

    /**
     * Set editorialResponsibility
     *
     * @param \UserBundle\Entity\User $editorialResponsibility
     *
     * @return TrainingContent
     */
    public function setEditorialResponsibility(\UserBundle\Entity\User $editorialResponsibility = null)
    {
        $this->editorialResponsibility = $editorialResponsibility;

        return $this;
    }

    /**
     * Get editorialResponsibility
     *
     * @return \UserBundle\Entity\User
     */
    public function getEditorialResponsibility()
    {
        return $this->editorialResponsibility;
    }

    /**
     * Set exerciseImageToTranscribe
     *
     * @param string $exerciseImageToTranscribe
     *
     * @return TrainingContent
     */
    public function setExerciseImageToTranscribe($exerciseImageToTranscribe)
    {
        $this->exerciseImageToTranscribe = $exerciseImageToTranscribe;

        return $this;
    }

    /**
     * Get exerciseImageToTranscribe
     *
     * @return string
     */
    public function getExerciseImageToTranscribe()
    {
        return $this->exerciseImageToTranscribe;
    }

    /**
     * Set exerciseIsSmartTEI
     *
     * @param boolean $exerciseIsSmartTEI
     *
     * @return TrainingContent
     */
    public function setExerciseIsSmartTEI($exerciseIsSmartTEI)
    {
        $this->exerciseIsSmartTEI = $exerciseIsSmartTEI;

        return $this;
    }

    /**
     * Get exerciseIsSmartTEI
     *
     * @return boolean
     */
    public function getExerciseIsSmartTEI()
    {
        return $this->exerciseIsSmartTEI;
    }

    /**
     * Set exerciseIsAttributesManagement
     *
     * @param boolean $exerciseIsAttributesManagement
     *
     * @return TrainingContent
     */
    public function setExerciseIsAttributesManagement($exerciseIsAttributesManagement)
    {
        $this->exerciseIsAttributesManagement = $exerciseIsAttributesManagement;

        return $this;
    }

    /**
     * Get exerciseIsAttributesManagement
     *
     * @return boolean
     */
    public function getExerciseIsAttributesManagement()
    {
        return $this->exerciseIsAttributesManagement;
    }

    /**
     * Set exerciseTagsList
     *
     * @param array $exerciseTagsList
     *
     * @return TrainingContent
     */
    public function setExerciseTagsList($exerciseTagsList)
    {
        $this->exerciseTagsList = $exerciseTagsList;

        return $this;
    }

    /**
     * Get exerciseTagsList
     *
     * @return array
     */
    public function getExerciseTagsList()
    {
        return $this->exerciseTagsList;
    }

    /**
     * Set exerciseIsLiveRender
     *
     * @param boolean $exerciseIsLiveRender
     *
     * @return TrainingContent
     */
    public function setExerciseIsLiveRender($exerciseIsLiveRender)
    {
        $this->exerciseIsLiveRender = $exerciseIsLiveRender;

        return $this;
    }

    /**
     * Get exerciseIsLiveRender
     *
     * @return boolean
     */
    public function getExerciseIsLiveRender()
    {
        return $this->exerciseIsLiveRender;
    }

    /**
     * Set exerciseIsHelp
     *
     * @param boolean $exerciseIsHelp
     *
     * @return TrainingContent
     */
    public function setExerciseIsHelp($exerciseIsHelp)
    {
        $this->exerciseIsHelp = $exerciseIsHelp;

        return $this;
    }

    /**
     * Get exerciseIsHelp
     *
     * @return boolean
     */
    public function getExerciseIsHelp()
    {
        return $this->exerciseIsHelp;
    }

    /**
     * Set exerciseIsDocumentation
     *
     * @param boolean $exerciseIsDocumentation
     *
     * @return TrainingContent
     */
    public function setExerciseIsDocumentation($exerciseIsDocumentation)
    {
        $this->exerciseIsDocumentation = $exerciseIsDocumentation;

        return $this;
    }

    /**
     * Get exerciseIsDocumentation
     *
     * @return boolean
     */
    public function getExerciseIsDocumentation()
    {
        return $this->exerciseIsDocumentation;
    }

    /**
     * Set exerciseIsTaxonomy
     *
     * @param boolean $exerciseIsTaxonomy
     *
     * @return TrainingContent
     */
    public function setExerciseIsTaxonomy($exerciseIsTaxonomy)
    {
        $this->exerciseIsTaxonomy = $exerciseIsTaxonomy;

        return $this;
    }

    /**
     * Get exerciseIsTaxonomy
     *
     * @return boolean
     */
    public function getExerciseIsTaxonomy()
    {
        return $this->exerciseIsTaxonomy;
    }

    /**
     * Set exerciseIsBibliography
     *
     * @param boolean $exerciseIsBibliography
     *
     * @return TrainingContent
     */
    public function setExerciseIsBibliography($exerciseIsBibliography)
    {
        $this->exerciseIsBibliography = $exerciseIsBibliography;

        return $this;
    }

    /**
     * Get exerciseIsBibliography
     *
     * @return boolean
     */
    public function getExerciseIsBibliography()
    {
        return $this->exerciseIsBibliography;
    }

    /**
     * Set exerciseIsNotes
     *
     * @param boolean $exerciseIsNotes
     *
     * @return TrainingContent
     */
    public function setExerciseIsNotes($exerciseIsNotes)
    {
        $this->exerciseIsNotes = $exerciseIsNotes;

        return $this;
    }

    /**
     * Get exerciseIsNotes
     *
     * @return boolean
     */
    public function getExerciseIsNotes()
    {
        return $this->exerciseIsNotes;
    }

    /**
     * Set exerciseIsVersioning
     *
     * @param boolean $exerciseIsVersioning
     *
     * @return TrainingContent
     */
    public function setExerciseIsVersioning($exerciseIsVersioning)
    {
        $this->exerciseIsVersioning = $exerciseIsVersioning;

        return $this;
    }

    /**
     * Get exerciseIsVersioning
     *
     * @return boolean
     */
    public function getExerciseIsVersioning()
    {
        return $this->exerciseIsVersioning;
    }

    /**
     * Set exerciseIsComplexFields
     *
     * @param boolean $exerciseIsComplexFields
     *
     * @return TrainingContent
     */
    public function setExerciseIsComplexFields($exerciseIsComplexFields)
    {
        $this->exerciseIsComplexFields = $exerciseIsComplexFields;

        return $this;
    }

    /**
     * Get exerciseIsComplexFields
     *
     * @return boolean
     */
    public function getExerciseIsComplexFields()
    {
        return $this->exerciseIsComplexFields;
    }

    /**
     * Set exerciseCorrectionTranscript
     *
     * @param string $exerciseCorrectionTranscript
     *
     * @return TrainingContent
     */
    public function setExerciseCorrectionTranscript($exerciseCorrectionTranscript)
    {
        $this->exerciseCorrectionTranscript = $exerciseCorrectionTranscript;

        return $this;
    }

    /**
     * Get exerciseCorrectionTranscript
     *
     * @return string
     */
    public function getExerciseCorrectionTranscript()
    {
        return $this->exerciseCorrectionTranscript;
    }

    /**
     * Set exerciseCorrectionErrorsToAvoid
     *
     * @param string $exerciseCorrectionErrorsToAvoid
     *
     * @return TrainingContent
     */
    public function setExerciseCorrectionErrorsToAvoid($exerciseCorrectionErrorsToAvoid)
    {
        $this->exerciseCorrectionErrorsToAvoid = $exerciseCorrectionErrorsToAvoid;

        return $this;
    }

    /**
     * Get exerciseCorrectionErrorsToAvoid
     *
     * @return string
     */
    public function getExerciseCorrectionErrorsToAvoid()
    {
        return $this->exerciseCorrectionErrorsToAvoid;
    }

    /**
     * Set internalGoal
     *
     * @param string $internalGoal
     *
     * @return TrainingContent
     */
    public function setInternalGoal($internalGoal)
    {
        $this->internalGoal = $internalGoal;

        return $this;
    }

    /**
     * Get internalGoal
     *
     * @return string
     */
    public function getInternalGoal()
    {
        return $this->internalGoal;
    }

    /**
     * Set exerciseHeader
     *
     * @param string $exerciseHeader
     *
     * @return TrainingContent
     */
    public function setExerciseHeader($exerciseHeader)
    {
        $this->exerciseHeader = $exerciseHeader;

        return $this;
    }

    /**
     * Get exerciseHeader
     *
     * @return string
     */
    public function getExerciseHeader()
    {
        return $this->exerciseHeader;
    }
}
