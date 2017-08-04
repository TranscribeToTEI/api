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
     * @Serializer\Groups({"full", "parent"})
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Entity", mappedBy="will")
     * @ORM\JoinColumn(nullable=true)
     */
    private $entity;

    /**
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\NotBlank(message = "La date de la minute ne peut pas être vide")
     * @Assert\Date(message = "La date de la minute n'est pas valide")
     *
     * @ORM\Column(name="minuteDate", type="date")
     */
    private $minuteDate;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\NotBlank(message = "La date d'écriture du testament ne peut pas être vide")
     * @Assert\Date(message = "La date d'écriture du testament n'est pas valide")
     *
     * @ORM\Column(name="willWritingDate", type="date")
     */
    private $willWritingDate;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumn(nullable=true)
     */
    private $willWritingPlace;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "testator"})
     * @Gedmo\Versioned
     *
     * @Assert\NotBlank(message = "Le champ testateur ne peut pas être vide")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Testator", inversedBy="wills", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $testator;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $createUser;

    /**
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Gedmo\Versioned
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $updateUser;

    /**
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
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
     * Set title
     *
     * @param string $title
     *
     * @return Will
     * @ORM\PrePersist
     */
    public function setTitle($title)
    {
        $this->title = "Testament ".$this->getCallNumber();

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
     * @param \DateTime $minuteDate
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
     * @return \DateTime
     */
    public function getMinuteDate()
    {
        return $this->minuteDate;
    }

    /**
     * Set willWritingDate
     *
     * @param \DateTime $willWritingDate
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
     * @return \DateTime
     */
    public function getWillWritingDate()
    {
        return $this->willWritingDate;
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
     * Set testator
     *
     * @param \AppBundle\Entity\Testator $testator
     *
     * @return Will
     */
    public function setTestator(\AppBundle\Entity\Testator $testator = null)
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
