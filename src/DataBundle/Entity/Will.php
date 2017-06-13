<?php

namespace DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use DataBundle\Entity\Entity;
use DataBundle\Entity\Testator;

/**
 * Will
 *
 * @ORM\Table(name="will")
 * @ORM\Entity(repositoryClass="DataBundle\Repository\WillRepository")
 */
class Will
{
    /**
     * @Serializer\Since("1.0")
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
     *
     * @ORM\OneToOne(targetEntity="Entity", mappedBy="will")
     * @ORM\JoinColumn(nullable=true)
     */
    private $entity;

    /**
     * @Serializer\Since("1.0")
     *
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @Serializer\Since("1.0")
     *
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @Serializer\Since("1.0")
     *
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\Date()
     *
     * @ORM\Column(name="minuteDate", type="date")
     */
    private $minuteDate;

    /**
     * @Serializer\Since("1.0")
     *
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\Date()
     *
     * @ORM\Column(name="willWritingDate", type="date")
     */
    private $willWritingDate;

    /**
     * @Serializer\Since("1.0")
     *
     * @var string
     *
     * @ORM\Column(name="willWritingPlace", type="string", length=255, nullable=true)
     */
    private $willWritingPlace;

    /**
     * @Serializer\Since("1.0")
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Testator", inversedBy="wills", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="testator_id", referencedColumnName="id")
     */
    private $testator;

    /**
     * @Serializer\Since("1.0")
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $createUser;

    /**
     * @Serializer\Since("1.0")
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
     * @param \DataBundle\Entity\Entity $entity
     *
     * @return Will
     */
    public function setEntity(\DataBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return \DataBundle\Entity\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set testator
     *
     * @param \DataBundle\Entity\Testator $testator
     *
     * @return Will
     */
    public function setTestator(\DataBundle\Entity\Testator $testator = null)
    {
        $this->testator = $testator;

        return $this;
    }

    /**
     * Get testator
     *
     * @return \DataBundle\Entity\Testator
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
     * Set number
     *
     * @param string $number
     *
     * @return Will
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }
}
