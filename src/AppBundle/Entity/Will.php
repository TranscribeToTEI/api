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
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_will",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "patch",
 *      href = @Hateoas\Route(
 *          "patch_will",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "remove_will",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 */
class Will
{
    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
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
     *
     * @ORM\OneToOne(targetEntity="Entity", mappedBy="will")
     * @ORM\JoinColumn(nullable=true)
     */
    private $entity;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var string
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="call_number", type="string", length=255)
     */
    private $callNumber;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var string
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\Date()
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="minuteDate", type="date")
     */
    private $minuteDate;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\Date()
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="willWritingDate", type="date")
     */
    private $willWritingDate;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var string
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="willWritingPlace", type="string", length=255, nullable=true)
     */
    private $willWritingPlace;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Testator", inversedBy="wills", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="testator_id", referencedColumnName="id")
     */
    private $testator;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $createUser;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var \Datetime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createDate", type="datetime", nullable=false)
     */
    protected $createDate;


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
     * Set willWritingPlace
     *
     * @param string $willWritingPlace
     *
     * @return Will
     */
    public function setWillWritingPlace($willWritingPlace)
    {
        $this->willWritingPlace = $willWritingPlace;

        return $this;
    }

    /**
     * Get willWritingPlace
     *
     * @return string
     */
    public function getWillWritingPlace()
    {
        return $this->willWritingPlace;
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
}
