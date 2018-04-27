<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Place
 *
 * @ORM\Table(name="place")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_place",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "place-links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_place",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "place-links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "patch",
 *      href = @Hateoas\Route(
 *          "patch_place",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "place-links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "remove_place",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "place-links"}
 *     )
 * )
 * @Hateoas\Relation(
 *     "version",
 *     embedded = @Hateoas\Embedded("expr(service('app.versioning').getVersions(object))"),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "versioning"}
 *     )
 * )
 * @Hateoas\Relation(
 *     "testators",
 *     embedded = @Hateoas\Embedded("expr(service('app.place').getTestators(object))"),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "content", "taxonomyLinks", "index"},
 *          maxDepth = 2
 *     )
 * )
 */
class Place
{
    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "place-id", "id", "index", "search", "infoWill"})
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Place name
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "index", "infoWill", "taxonomyView", "adminEntity", "taxonomyList"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="indexName", type="string", length=255, nullable=true)
     */
    private $indexName;

    /**
     * Place name
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "taxonomyView", "adminEntity"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * Place name
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView", "taxonomyList"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="frenchDepartement", type="string", length=255, nullable=true)
     */
    private $frenchDepartement;

    /**
     * Place name
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView", "taxonomyList"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="frenchRegion", type="string", length=255, nullable=true)
     */
    private $frenchRegion;

    /**
     * Place name
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView", "taxonomyList"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * Place name
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView", "taxonomyList"})
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * Description of your place
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * Geonames identifier of your place
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView", "taxonomyList"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="geonamesId", type="string", length=255, nullable=true)
     */
    private $geonamesId;

    /**
     * Geographical coordinates of your place
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "taxonomyView"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="geographicalCoordinates", type="string", length=255, nullable=true)
     */
    private $geographicalCoordinates;

    /**
     * Is the current version an official version of your project team
     *
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
     * @Serializer\Groups({"full", "metadata", "place-metadata"})
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
     * @Serializer\Groups({"full", "metadata", "place-metadata"})
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
     * @Serializer\Groups({"full", "metadata", "place-metadata"})
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
     * @Serializer\Groups({"full", "metadata", "place-metadata"})
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
     * @Serializer\Groups({"full", "metadata", "place-metadata"})
     * @Gedmo\Versioned
     *
     * @Assert\Type("string")
     *
     * @var string
     *
     * @ORM\Column(name="updateComment", type="text", length=255, nullable=false)
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

    }

    public function set($property, $value) {
        $this->{$property} = $value;

        return $this;
    }

    public function add($property, $value) {
        $this->{$property}[] = $value;

        return $this;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Place
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
     * Set geonamesId
     *
     * @param string $geonamesId
     *
     * @return Place
     */
    public function setGeonamesId($geonamesId)
    {
        $this->geonamesId = $geonamesId;

        return $this;
    }

    /**
     * Get geonamesId
     *
     * @return string
     */
    public function getGeonamesId()
    {
        return $this->geonamesId;
    }

    /**
     * Set geographicalCoordinates
     *
     * @param string $geographicalCoordinates
     *
     * @return Place
     */
    public function setGeographicalCoordinates($geographicalCoordinates)
    {
        $this->geographicalCoordinates = $geographicalCoordinates;

        return $this;
    }

    /**
     * Get geographicalCoordinates
     *
     * @return string
     */
    public function getGeographicalCoordinates()
    {
        return $this->geographicalCoordinates;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Place
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
     * @return Place
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
     * @return Place
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
     * @return Place
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
     * @return Place
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
     * Set isOfficialVersion
     *
     * @param boolean $isOfficialVersion
     *
     * @return Place
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
     * Set indexName
     *
     * @param string $indexName
     *
     * @return Place
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
     * Set name
     *
     * @param string $name
     *
     * @return Place
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
     * Set frenchDepartement
     *
     * @param string $frenchDepartement
     *
     * @return Place
     */
    public function setFrenchDepartement($frenchDepartement)
    {
        $this->frenchDepartement = $frenchDepartement;

        return $this;
    }

    /**
     * Get frenchDepartement
     *
     * @return string
     */
    public function getFrenchDepartement()
    {
        return $this->frenchDepartement;
    }

    /**
     * Set frenchRegion
     *
     * @param string $frenchRegion
     *
     * @return Place
     */
    public function setFrenchRegion($frenchRegion)
    {
        $this->frenchRegion = $frenchRegion;

        return $this;
    }

    /**
     * Get frenchRegion
     *
     * @return string
     */
    public function getFrenchRegion()
    {
        return $this->frenchRegion;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Place
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Place
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
}
