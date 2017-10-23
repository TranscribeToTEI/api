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
     * @Assert\Url()
     *
     * @ORM\Column(name="illustration", type="text", nullable=true)
     */
    private $illustration;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="orderInTraining", type="integer")
     */
    private $orderInTraining;

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
}
