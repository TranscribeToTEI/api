<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

use AppBundle\Entity\Entity;
use JMS\Serializer\Annotation as Serializer;

/**
 * CommentLog
 *
 * @ORM\Table(name="comment_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentLogRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_comment_log",
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
 *          "update_comment_log",
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
 *          "patch_comment_log",
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
 *          "remove_comment_log",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class CommentLog
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
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("bool")
     *
     * @ORM\Column(name="isReadByAdmin", type="boolean")
     */
    private $isReadByAdmin;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("bool")
     *
     * @ORM\Column(name="isPrivateThread", type="boolean")
     */
    private $isPrivateThread;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("bool")
     *
     * @ORM\Column(name="isReadByRecipient", type="boolean")
     */
    private $isReadByRecipient;


    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Serializer\MaxDepth(1)
     *
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment\Thread")
     * @ORM\JoinColumn(nullable=true)
     */
    private $thread;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Serializer\MaxDepth(1)
     *
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment\Comment")
     * @ORM\JoinColumn(nullable=true)
     */
    private $comment;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Serializer\MaxDepth(1)
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
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
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updateDate", type="datetime", nullable=false)
     */
    protected $updateDate;


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
     * Set isReadByAdmin
     *
     * @param boolean $isReadByAdmin
     *
     * @return CommentLog
     */
    public function setIsReadByAdmin($isReadByAdmin)
    {
        $this->isReadByAdmin = $isReadByAdmin;

        return $this;
    }

    /**
     * Get isReadByAdmin
     *
     * @return boolean
     */
    public function getIsReadByAdmin()
    {
        return $this->isReadByAdmin;
    }

    /**
     * Set isPrivateThread
     *
     * @param boolean $isPrivateThread
     *
     * @return CommentLog
     */
    public function setIsPrivateThread($isPrivateThread)
    {
        $this->isPrivateThread = $isPrivateThread;

        return $this;
    }

    /**
     * Get isPrivateThread
     *
     * @return boolean
     */
    public function getIsPrivateThread()
    {
        return $this->isPrivateThread;
    }

    /**
     * Set isReadByRecipient
     *
     * @param boolean $isReadByRecipient
     *
     * @return CommentLog
     */
    public function setIsReadByRecipient($isReadByRecipient)
    {
        $this->isReadByRecipient = $isReadByRecipient;

        return $this;
    }

    /**
     * Get isReadByRecipient
     *
     * @return boolean
     */
    public function getIsReadByRecipient()
    {
        return $this->isReadByRecipient;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return CommentLog
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
     * @return CommentLog
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
     * Set thread
     *
     * @param \AppBundle\Entity\Comment\Thread $thread
     *
     * @return CommentLog
     */
    public function setThread(\AppBundle\Entity\Comment\Thread $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \AppBundle\Entity\Comment\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set comment
     *
     * @param \AppBundle\Entity\Comment\Comment $comment
     *
     * @return CommentLog
     */
    public function setComment(\AppBundle\Entity\Comment\Comment $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \AppBundle\Entity\Comment\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createUser
     *
     * @param \UserBundle\Entity\User $createUser
     *
     * @return CommentLog
     */
    public function setCreateUser(\UserBundle\Entity\User $createUser)
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
     * @return CommentLog
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
