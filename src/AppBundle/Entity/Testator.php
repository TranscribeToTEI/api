<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

use AppBundle\Entity\Will;
use JMS\Serializer\Annotation as Serializer;

/**
 * Testator
 *
 * @ORM\Table(name="testator")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TestatorRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_testator",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_testator",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "patch",
 *      href = @Hateoas\Route(
 *          "patch_testator",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "remove_testator",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 */
class Testator
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Will", mappedBy="testator")
     * @ORM\JoinColumn(nullable=true)
     */
    private $wills;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="fullName", type="string", length=255)
     */
    private $fullName;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="firstnames", type="string", length=255)
     */
    private $firstnames;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     */
    private $profession;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\Date()
     * @Gedmo\Versioned
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dateOfBirth", type="date")
     */
    private $dateOfBirth;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="placeOfBirth", type="string", length=255)
     */
    private $placeOfBirth;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\Date()
     * @Gedmo\Versioned
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dateOfDeath", type="date")
     */
    private $dateOfDeath;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="placeOfDeath", type="string", length=255)
     */
    private $placeOfDeath;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="deathMention", type="string", length=255)
     */
    private $deathMention;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\Url()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="memoireDesHommes", type="string", length=255)
     */
    private $memoireDesHommes;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="regiment", type="string", length=255, nullable=true)
     */
    private $regiment;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="rank", type="string", length=255, nullable=true)
     */
    private $rank;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $createUser;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var \DateTime
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
     * Constructor
     */
    public function __construct()
    {
        $this->wills = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return Testator
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return Testator
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set firstnames
     *
     * @param string $firstnames
     *
     * @return Testator
     */
    public function setFirstnames($firstnames)
    {
        $this->firstnames = $firstnames;

        return $this;
    }

    /**
     * Get firstnames
     *
     * @return string
     */
    public function getFirstnames()
    {
        return $this->firstnames;
    }

    /**
     * Set profession
     *
     * @param string $profession
     *
     * @return Testator
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Testator
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     *
     * @return Testator
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set placeOfBirth
     *
     * @param string $placeOfBirth
     *
     * @return Testator
     */
    public function setPlaceOfBirth($placeOfBirth)
    {
        $this->placeOfBirth = $placeOfBirth;

        return $this;
    }

    /**
     * Get placeOfBirth
     *
     * @return string
     */
    public function getPlaceOfBirth()
    {
        return $this->placeOfBirth;
    }

    /**
     * Set dateOfDeath
     *
     * @param \DateTime $dateOfDeath
     *
     * @return Testator
     */
    public function setDateOfDeath($dateOfDeath)
    {
        $this->dateOfDeath = $dateOfDeath;

        return $this;
    }

    /**
     * Get dateOfDeath
     *
     * @return \DateTime
     */
    public function getDateOfDeath()
    {
        return $this->dateOfDeath;
    }

    /**
     * Set placeOfDeath
     *
     * @param string $placeOfDeath
     *
     * @return Testator
     */
    public function setPlaceOfDeath($placeOfDeath)
    {
        $this->placeOfDeath = $placeOfDeath;

        return $this;
    }

    /**
     * Get placeOfDeath
     *
     * @return string
     */
    public function getPlaceOfDeath()
    {
        return $this->placeOfDeath;
    }

    /**
     * Set deathMention
     *
     * @param string $deathMention
     *
     * @return Testator
     */
    public function setDeathMention($deathMention)
    {
        $this->deathMention = $deathMention;

        return $this;
    }

    /**
     * Get deathMention
     *
     * @return string
     */
    public function getDeathMention()
    {
        return $this->deathMention;
    }

    /**
     * Set memoireDesHommes
     *
     * @param string $memoireDesHommes
     *
     * @return Testator
     */
    public function setMemoireDesHommes($memoireDesHommes)
    {
        $this->memoireDesHommes = $memoireDesHommes;

        return $this;
    }

    /**
     * Get memoireDesHommes
     *
     * @return string
     */
    public function getMemoireDesHommes()
    {
        return $this->memoireDesHommes;
    }

    /**
     * Set regiment
     *
     * @param string $regiment
     *
     * @return Testator
     */
    public function setRegiment($regiment)
    {
        $this->regiment = $regiment;

        return $this;
    }

    /**
     * Get regiment
     *
     * @return string
     */
    public function getRegiment()
    {
        return $this->regiment;
    }

    /**
     * Set rank
     *
     * @param string $rank
     *
     * @return Testator
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Testator
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
     * Add will
     *
     * @param \AppBundle\Entity\Will $will
     *
     * @return Testator
     */
    public function addWill(\AppBundle\Entity\Will $will)
    {
        $this->wills[] = $will;

        return $this;
    }

    /**
     * Remove will
     *
     * @param \AppBundle\Entity\Will $will
     */
    public function removeWill(\AppBundle\Entity\Will $will)
    {
        $this->wills->removeElement($will);
    }

    /**
     * Get wills
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWills()
    {
        return $this->wills;
    }

    /**
     * Set createUser
     *
     * @param \UserBundle\Entity\User $createUser
     *
     * @return Testator
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
}
