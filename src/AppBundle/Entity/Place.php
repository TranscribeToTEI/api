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
     * Names of the place > Related to PlaceName entities
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "infoWill", "taxonomyView", "adminEntity"})
     *
     * @Assert\NotBlank()
     * @Serializer\MaxDepth(2)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeName", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $names;

    /**
     * Departements (relevant for France) of your place > Related to PlaceName entities
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView", "taxonomyList"})
     *
     * @Serializer\MaxDepth(2)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeDepartement", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $frenchDepartements;

    /**
     * Regions (relevant for France) of your place > Related to PlaceName entities
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView", "taxonomyList"})
     *
     * @Serializer\MaxDepth(2)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeRegion", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $frenchRegions;

    /**
     * Cities (relevant for places which are not city) of your place > Related to PlaceName entities
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView", "taxonomyList"})
     *
     * @Serializer\MaxDepth(2)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeCity", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $cities;

    /**
     * Countries of your place > Related to PlaceName entities
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView", "taxonomyList"})
     *
     * @Serializer\MaxDepth(2)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeCountry", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $countries;

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
        $this->names = new \Doctrine\Common\Collections\ArrayCollection();
        $this->frenchDepartements = new \Doctrine\Common\Collections\ArrayCollection();
        $this->frenchRegions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->countries = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add name
     *
     * @param \AppBundle\Entity\PlaceName $name
     *
     * @return Place
     */
    public function addName(\AppBundle\Entity\PlaceName $name)
    {
        $this->names[] = $name;

        return $this;
    }

    /**
     * Remove name
     *
     * @param \AppBundle\Entity\PlaceName $name
     */
    public function removeName(\AppBundle\Entity\PlaceName $name)
    {
        $this->names->removeElement($name);
    }

    /**
     * Get names
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Add frenchDepartement
     *
     * @param \AppBundle\Entity\PlaceName $frenchDepartement
     *
     * @return Place
     */
    public function addFrenchDepartement(\AppBundle\Entity\PlaceName $frenchDepartement)
    {
        $this->frenchDepartements[] = $frenchDepartement;

        return $this;
    }

    /**
     * Remove frenchDepartement
     *
     * @param \AppBundle\Entity\PlaceName $frenchDepartement
     */
    public function removeFrenchDepartement(\AppBundle\Entity\PlaceName $frenchDepartement)
    {
        $this->frenchDepartements->removeElement($frenchDepartement);
    }

    /**
     * Get frenchDepartements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFrenchDepartements()
    {
        return $this->frenchDepartements;
    }

    /**
     * Add frenchRegion
     *
     * @param \AppBundle\Entity\PlaceName $frenchRegion
     *
     * @return Place
     */
    public function addFrenchRegion(\AppBundle\Entity\PlaceName $frenchRegion)
    {
        $this->frenchRegions[] = $frenchRegion;

        return $this;
    }

    /**
     * Remove frenchRegion
     *
     * @param \AppBundle\Entity\PlaceName $frenchRegion
     */
    public function removeFrenchRegion(\AppBundle\Entity\PlaceName $frenchRegion)
    {
        $this->frenchRegions->removeElement($frenchRegion);
    }

    /**
     * Get frenchRegions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFrenchRegions()
    {
        return $this->frenchRegions;
    }

    /**
     * Add city
     *
     * @param \AppBundle\Entity\PlaceName $city
     *
     * @return Place
     */
    public function addCity(\AppBundle\Entity\PlaceName $city)
    {
        $this->cities[] = $city;

        return $this;
    }

    /**
     * Remove city
     *
     * @param \AppBundle\Entity\PlaceName $city
     */
    public function removeCity(\AppBundle\Entity\PlaceName $city)
    {
        $this->cities->removeElement($city);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Add country
     *
     * @param \AppBundle\Entity\PlaceName $country
     *
     * @return Place
     */
    public function addCountry(\AppBundle\Entity\PlaceName $country)
    {
        $this->countries[] = $country;

        return $this;
    }

    /**
     * Remove country
     *
     * @param \AppBundle\Entity\PlaceName $country
     */
    public function removeCountry(\AppBundle\Entity\PlaceName $country)
    {
        $this->countries->removeElement($country);
    }

    /**
     * Get countries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCountries()
    {
        return $this->countries;
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
}
