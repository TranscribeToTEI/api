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
     * Wills of the testator
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "wills"})
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Will", mappedBy="testator")
     * @ORM\JoinColumn(nullable=true)
     */
    private $wills;

    /**
     * Name of the testator (concatenation of the firstnames and the surname)
     *
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
     * Name of indexation of the testator
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="indexName", type="string", length=255)
     */
    private $indexName;

    /**
     * Surname of the testator
     *
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
     * Firstnames of the testator
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="firstnames", type="text")
     */
    private $firstnames;

    /**
     * Other names of the testator (pseudo...)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="otherNames", type="text", nullable=true)
     */
    private $otherNames;

    /**
     * Profession of the testator
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="profession", type="text", nullable=true)
     */
    private $profession;

    /**
     * Address number of the testator
     *
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
     * Address street of the testator
     *
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
     * Address district of the testator
     *
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
     * Address city of the testator, linked to a place
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(3)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumn(nullable=true)
     */
    private $addressCity;

    /**
     * Full address of the testator
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="addressString", type="text", nullable=true)
     */
    private $addressString;

    /**
     * Full date of birth of the testator (string)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="dateOfBirthString", type="text", nullable=true)
     */
    private $dateOfBirthString;

    /**
     * Normalized date of birth
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\Date()
     *
     * @ORM\Column(name="dateOfBirthNormalized", type="date", nullable=true)
     */
    private $dateOfBirthNormalized;

    /**
     * If date of birth is an interval, end of the interval
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\Date()
     *
     * @ORM\Column(name="dateOfBirthEndNormalized", type="date", nullable=true)
     */
    private $dateOfBirthEndNormalized;

    /**
     * Year of birth (for index)
     *
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
     * Normalized place of birth (related to a place)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(3)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumn(nullable=true)
     */
    private $placeOfBirthNormalized;

    /**
     * Full place of birth (string)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="placeOfBirthString", type="text", nullable=true)
     */
    private $placeOfBirthString;

    /**
     * Full date of death (string)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="dateOfDeathString", type="text", nullable=false)
     */
    private $dateOfDeathString;

    /**
     * Normalized date of death
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\Date()
     * @Assert\NotNull(message = "La date de décès normalisée du testament ne peut pas être vide")
     *
     * @ORM\Column(name="dateOfDeathNormalized", type="date", nullable=false)
     */
    private $dateOfDeathNormalized;

    /**
     * If date of death is an interval, end of the interval
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     * @Assert\Date()
     *
     * @ORM\Column(name="dateOfDeathEndNormalized", type="date", nullable=true)
     */
    private $dateOfDeathEndNormalized;

    /**
     * Year of death, used for index
     *
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
     * Normalized place of death (related to a place)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(3)
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumn(nullable=false)
     */
    private $placeOfDeathNormalized;

    /**
     * Full place of death (string)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="placeOfDeathString", type="text", nullable=false)
     */
    private $placeOfDeathString;

    /**
     * Death mention of the testator
     *
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
     * A testator can have multiple notices on Memoire des Hommes, so this is an array, not a string. If there is no link found, provide the search date of the link.
     *
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
     * Normalized military unit of the testator (linked to military unit)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MilitaryUnit")
     * @ORM\JoinColumn(nullable=true)
     */
    private $militaryUnitNormalized;

    /**
     * Full military unit of the testator (string)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="militaryUnitString", type="text", nullable=true)
     */
    private $militaryUnitString;

    /**
     * Rank of the testator in his military unit
     *
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
     * Biography of the testator
     *
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
     * Picture of the testator
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="picture", type="text", nullable=true)
     */
    private $picture;

    /**
     * Is the current version an official version of the project team
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Gedmo\Versioned
     *
     * @var bool
     *
     * @ORM\Column(name="isOfficialVersion", type="boolean", options={"default" : false})
     */
    private $isOfficialVersion;

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
     * Set
     *
     * @param string $field
     * @param string $value
     *
     * @return Testator
     */
    public function set($field, $value)
    {
        $this->{$field} = $value;

        return $this;
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
     * Set indexName
     *
     * @param string $indexName
     *
     * @return Testator
     */
    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;

        return $this;
    }

    /**
     * Get indexName
     *
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
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
     * Set otherNames
     *
     * @param string $otherNames
     *
     * @return Testator
     */
    public function setOtherNames($otherNames)
    {
        $this->otherNames = $otherNames;

        return $this;
    }

    /**
     * Get otherNames
     *
     * @return string
     */
    public function getOtherNames()
    {
        return $this->otherNames;
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
     * Set addressString
     *
     * @param string $addressString
     *
     * @return Testator
     */
    public function setAddressString($addressString)
    {
        $this->addressString = $addressString;

        return $this;
    }

    /**
     * Get addressString
     *
     * @return string
     */
    public function getAddressString()
    {
        return $this->addressString;
    }

    /**
     * Set dateOfBirthString
     *
     * @param string $dateOfBirthString
     *
     * @return Testator
     */
    public function setDateOfBirthString($dateOfBirthString)
    {
        $this->dateOfBirthString = $dateOfBirthString;

        return $this;
    }

    /**
     * Get dateOfBirthString
     *
     * @return string
     */
    public function getDateOfBirthString()
    {
        return $this->dateOfBirthString;
    }

    /**
     * Set dateOfBirthNormalized
     *
     * @param \DateTime $dateOfBirthNormalized
     *
     * @return Testator
     */
    public function setDateOfBirthNormalized($dateOfBirthNormalized)
    {
        $this->dateOfBirthNormalized = $dateOfBirthNormalized;

        return $this;
    }

    /**
     * Get dateOfBirthNormalized
     *
     * @return \DateTime
     */
    public function getDateOfBirthNormalized()
    {
        return $this->dateOfBirthNormalized;
    }

    /**
     * Set dateOfBirthEndNormalized
     *
     * @param \DateTime $dateOfBirthEndNormalized
     *
     * @return Testator
     */
    public function setDateOfBirthEndNormalized($dateOfBirthEndNormalized)
    {
        $this->dateOfBirthEndNormalized = $dateOfBirthEndNormalized;

        return $this;
    }

    /**
     * Get dateOfBirthEndNormalized
     *
     * @return \DateTime
     */
    public function getDateOfBirthEndNormalized()
    {
        return $this->dateOfBirthEndNormalized;
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
     * Set placeOfBirthString
     *
     * @param string $placeOfBirthString
     *
     * @return Testator
     */
    public function setPlaceOfBirthString($placeOfBirthString)
    {
        $this->placeOfBirthString = $placeOfBirthString;

        return $this;
    }

    /**
     * Get placeOfBirthString
     *
     * @return string
     */
    public function getPlaceOfBirthString()
    {
        return $this->placeOfBirthString;
    }

    /**
     * Set dateOfDeathString
     *
     * @param string $dateOfDeathString
     *
     * @return Testator
     */
    public function setDateOfDeathString($dateOfDeathString)
    {
        $this->dateOfDeathString = $dateOfDeathString;

        return $this;
    }

    /**
     * Get dateOfDeathString
     *
     * @return string
     */
    public function getDateOfDeathString()
    {
        return $this->dateOfDeathString;
    }

    /**
     * Set dateOfDeathNormalized
     *
     * @param \DateTime $dateOfDeathNormalized
     *
     * @return Testator
     */
    public function setDateOfDeathNormalized($dateOfDeathNormalized)
    {
        $this->dateOfDeathNormalized = $dateOfDeathNormalized;

        return $this;
    }

    /**
     * Get dateOfDeathNormalized
     *
     * @return \DateTime
     */
    public function getDateOfDeathNormalized()
    {
        return $this->dateOfDeathNormalized;
    }

    /**
     * Set dateOfDeathEndNormalized
     *
     * @param \DateTime $dateOfDeathEndNormalized
     *
     * @return Testator
     */
    public function setDateOfDeathEndNormalized($dateOfDeathEndNormalized)
    {
        $this->dateOfDeathEndNormalized = $dateOfDeathEndNormalized;

        return $this;
    }

    /**
     * Get dateOfDeathEndNormalized
     *
     * @return \DateTime
     */
    public function getDateOfDeathEndNormalized()
    {
        return $this->dateOfDeathEndNormalized;
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
     * Set placeOfDeathString
     *
     * @param string $placeOfDeathString
     *
     * @return Testator
     */
    public function setPlaceOfDeathString($placeOfDeathString)
    {
        $this->placeOfDeathString = $placeOfDeathString;

        return $this;
    }

    /**
     * Get placeOfDeathString
     *
     * @return string
     */
    public function getPlaceOfDeathString()
    {
        return $this->placeOfDeathString;
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
     * Set militaryUnitString
     *
     * @param string $militaryUnitString
     *
     * @return Testator
     */
    public function setMilitaryUnitString($militaryUnitString)
    {
        $this->militaryUnitString = $militaryUnitString;

        return $this;
    }

    /**
     * Get militaryUnitString
     *
     * @return string
     */
    public function getMilitaryUnitString()
    {
        return $this->militaryUnitString;
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
     * Set picture
     *
     * @param string $picture
     *
     * @return Testator
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set isOfficialVersion
     *
     * @param boolean $isOfficialVersion
     *
     * @return Testator
     */
    public function setIsOfficialVersion($isOfficialVersion)
    {
        $this->isOfficialVersion = $isOfficialVersion;

        return $this;
    }

    /**
     * Get isOfficialVersion
     *
     * @return boolean
     */
    public function getIsOfficialVersion()
    {
        return $this->isOfficialVersion;
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
     * Set addressCity
     *
     * @param \AppBundle\Entity\Place $addressCity
     *
     * @return Testator
     */
    public function setAddressCity(\AppBundle\Entity\Place $addressCity = null)
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    /**
     * Get addressCity
     *
     * @return \AppBundle\Entity\Place
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Set placeOfBirthNormalized
     *
     * @param \AppBundle\Entity\Place $placeOfBirthNormalized
     *
     * @return Testator
     */
    public function setPlaceOfBirthNormalized(\AppBundle\Entity\Place $placeOfBirthNormalized = null)
    {
        $this->placeOfBirthNormalized = $placeOfBirthNormalized;

        return $this;
    }

    /**
     * Get placeOfBirthNormalized
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlaceOfBirthNormalized()
    {
        return $this->placeOfBirthNormalized;
    }

    /**
     * Set placeOfDeathNormalized
     *
     * @param \AppBundle\Entity\Place $placeOfDeathNormalized
     *
     * @return Testator
     */
    public function setPlaceOfDeathNormalized(\AppBundle\Entity\Place $placeOfDeathNormalized)
    {
        $this->placeOfDeathNormalized = $placeOfDeathNormalized;

        return $this;
    }

    /**
     * Get placeOfDeathNormalized
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlaceOfDeathNormalized()
    {
        return $this->placeOfDeathNormalized;
    }

    /**
     * Set militaryUnitNormalized
     *
     * @param \AppBundle\Entity\MilitaryUnit $militaryUnitNormalized
     *
     * @return Testator
     */
    public function setMilitaryUnitNormalized(\AppBundle\Entity\MilitaryUnit $militaryUnitNormalized = null)
    {
        $this->militaryUnitNormalized = $militaryUnitNormalized;

        return $this;
    }

    /**
     * Get militaryUnitNormalized
     *
     * @return \AppBundle\Entity\MilitaryUnit
     */
    public function getMilitaryUnitNormalized()
    {
        return $this->militaryUnitNormalized;
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
