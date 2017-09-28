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
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_testator",
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
 *          "patch_testator",
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
 *          "remove_testator",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 * @Hateoas\Relation(
 *     "version",
 *     embedded = @Hateoas\Embedded("expr(service('app.versioning').getVersions(object))"),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "versioning"}
 *     )
 * )
 */
class Testator
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
     * @Serializer\Groups({"full", "wills"})
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Will", mappedBy="testator")
     * @ORM\JoinColumn(nullable=true)
     */
    private $wills;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="firstnames", type="string", length=255)
     */
    private $firstnames;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     */
    private $profession;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Groups({"full", "content"})
     * @Serializer\Expose
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="addressNumber", type="string", length=255, nullable=true)
     */
    private $addressNumber;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Groups({"full", "content"})
     * @Serializer\Expose
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="addressStreet", type="string", length=255, nullable=true)
     */
    private $addressStreet;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Groups({"full", "content"})
     * @Serializer\Expose
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="addressDistrict", type="string", length=255, nullable=true)
     */
    private $addressDistrict;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Groups({"full", "content"})
     * @Serializer\Expose
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="addressCity", type="string", length=255, nullable=true)
     */
    private $addressCity;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     *
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="dateOfBirth", type="string", length=255)
     */
    private $dateOfBirth;

    /**
     * The field is used to index dates in search
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="yearOfBirth", type="string", length=5)
     */
    private $yearOfBirth;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumn(nullable=true)
     */
    private $placeOfBirth;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     *
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="dateOfDeath", type="string", length=255)
     */
    private $dateOfDeath;

    /**
     * The field is used to index dates in search
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="yearOfDeath", type="string", length=5)
     */
    private $yearOfDeath;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumn(nullable=false)
     */
    private $placeOfDeath;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="deathMention", type="string", length=255)
     */
    private $deathMention;

    /**
     * A testator can have multiple notices on Memoire des Hommes, so this is an array, not a string
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     *
     * @Gedmo\Versioned
     *
     * @var array
     *
     * @ORM\Column(name="memoireDesHommes", type="array")
     */
    private $memoireDesHommes;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MilitaryUnit")
     * @ORM\JoinColumn(nullable=true)
     */
    private $militaryUnit;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="rank", type="string", length=255, nullable=true)
     */
    private $rank;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * Constructor
     */
    public function __construct()
    {
        $this->wills = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Testator
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Set addressNumber
     *
     * @param string $addressNumber
     *
     * @return Testator
     */
    public function setAddressNumber($addressNumber)
    {
        $this->addressNumber = $addressNumber;

        return $this;
    }

    /**
     * Get addressNumber
     *
     * @return string
     */
    public function getAddressNumber()
    {
        return $this->addressNumber;
    }

    /**
     * Set addressStreet
     *
     * @param string $addressStreet
     *
     * @return Testator
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    /**
     * Get addressStreet
     *
     * @return string
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Set addressDistrict
     *
     * @param string $addressDistrict
     *
     * @return Testator
     */
    public function setAddressDistrict($addressDistrict)
    {
        $this->addressDistrict = $addressDistrict;

        return $this;
    }

    /**
     * Get addressDistrict
     *
     * @return string
     */
    public function getAddressDistrict()
    {
        return $this->addressDistrict;
    }

    /**
     * Set addressCity
     *
     * @param string $addressCity
     *
     * @return Testator
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    /**
     * Get addressCity
     *
     * @return string
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Set dateOfBirth
     *
     * @param string $dateOfBirth
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
     * @return string
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set yearOfBirth
     *
     * @param string $yearOfBirth
     *
     * @return Testator
     */
    public function setYearOfBirth($yearOfBirth)
    {
        $this->yearOfBirth = $yearOfBirth;

        return $this;
    }

    /**
     * Get yearOfBirth
     *
     * @return string
     */
    public function getYearOfBirth()
    {
        return $this->yearOfBirth;
    }

    /**
     * Set dateOfDeath
     *
     * @param string $dateOfDeath
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
     * @return string
     */
    public function getDateOfDeath()
    {
        return $this->dateOfDeath;
    }

    /**
     * Set yearOfDeath
     *
     * @param string $yearOfDeath
     *
     * @return Testator
     */
    public function setYearOfDeath($yearOfDeath)
    {
        $this->yearOfDeath = $yearOfDeath;

        return $this;
    }

    /**
     * Get yearOfDeath
     *
     * @return string
     */
    public function getYearOfDeath()
    {
        return $this->yearOfDeath;
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
     * @param array $memoireDesHommes
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
     * @return array
     */
    public function getMemoireDesHommes()
    {
        return $this->memoireDesHommes;
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
     * Set description
     *
     * @param string $description
     *
     * @return Testator
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Testator
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
     * @return Testator
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
     * Set placeOfBirth
     *
     * @param \AppBundle\Entity\Place $placeOfBirth
     *
     * @return Testator
     */
    public function setPlaceOfBirth(\AppBundle\Entity\Place $placeOfBirth = null)
    {
        $this->placeOfBirth = $placeOfBirth;

        return $this;
    }

    /**
     * Get placeOfBirth
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlaceOfBirth()
    {
        return $this->placeOfBirth;
    }

    /**
     * Set placeOfDeath
     *
     * @param \AppBundle\Entity\Place $placeOfDeath
     *
     * @return Testator
     */
    public function setPlaceOfDeath(\AppBundle\Entity\Place $placeOfDeath)
    {
        $this->placeOfDeath = $placeOfDeath;

        return $this;
    }

    /**
     * Get placeOfDeath
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlaceOfDeath()
    {
        return $this->placeOfDeath;
    }

    /**
     * Set militaryUnit
     *
     * @param \AppBundle\Entity\MilitaryUnit $militaryUnit
     *
     * @return Testator
     */
    public function setMilitaryUnit(\AppBundle\Entity\MilitaryUnit $militaryUnit = null)
    {
        $this->militaryUnit = $militaryUnit;

        return $this;
    }

    /**
     * Get militaryUnit
     *
     * @return \AppBundle\Entity\MilitaryUnit
     */
    public function getMilitaryUnit()
    {
        return $this->militaryUnit;
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

    /**
     * Set updateUser
     *
     * @param \UserBundle\Entity\User $updateUser
     *
     * @return Testator
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
