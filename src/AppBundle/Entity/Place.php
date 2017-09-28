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
 *          groups={"full", "links"}
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
 *          groups={"full", "links"}
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
 *          groups={"full", "links"}
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
 * @Hateoas\Relation(
 *     "testators",
 *     embedded = @Hateoas\Embedded("expr(service('app.place').getTestators(object))"),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "content"}
 *     )
 * )
 */
class Place
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
     * @Assert\NotBlank()
     * @Serializer\MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeName", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $name;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Serializer\MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeDepartement", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $frenchDepartement;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Serializer\MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeRegion", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $frenchRegion;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Serializer\MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeCity", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $city;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Serializer\MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlaceName", mappedBy="placeCountry", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $country;

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
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="geonamesId", type="string", length=255, nullable=true)
     */
    private $geonamesId;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="geographicalCoordinates", type="string", length=255, nullable=true)
     */
    private $geographicalCoordinates;

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
     * Constructor
     */
    public function __construct()
    {
        $this->name = new \Doctrine\Common\Collections\ArrayCollection();
        $this->frenchDepartement = new \Doctrine\Common\Collections\ArrayCollection();
        $this->frenchRegion = new \Doctrine\Common\Collections\ArrayCollection();
        $this->city = new \Doctrine\Common\Collections\ArrayCollection();
        $this->country = new \Doctrine\Common\Collections\ArrayCollection();
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
        $this->name[] = $name;

        return $this;
    }

    /**
     * Remove name
     *
     * @param \AppBundle\Entity\PlaceName $name
     */
    public function removeName(\AppBundle\Entity\PlaceName $name)
    {
        $this->name->removeElement($name);
    }

    /**
     * Get name
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getName()
    {
        return $this->name;
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
        $this->frenchDepartement[] = $frenchDepartement;

        return $this;
    }

    /**
     * Remove frenchDepartement
     *
     * @param \AppBundle\Entity\PlaceName $frenchDepartement
     */
    public function removeFrenchDepartement(\AppBundle\Entity\PlaceName $frenchDepartement)
    {
        $this->frenchDepartement->removeElement($frenchDepartement);
    }

    /**
     * Get frenchDepartement
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFrenchDepartement()
    {
        return $this->frenchDepartement;
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
        $this->frenchRegion[] = $frenchRegion;

        return $this;
    }

    /**
     * Remove frenchRegion
     *
     * @param \AppBundle\Entity\PlaceName $frenchRegion
     */
    public function removeFrenchRegion(\AppBundle\Entity\PlaceName $frenchRegion)
    {
        $this->frenchRegion->removeElement($frenchRegion);
    }

    /**
     * Get frenchRegion
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFrenchRegion()
    {
        return $this->frenchRegion;
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
        $this->city[] = $city;

        return $this;
    }

    /**
     * Remove city
     *
     * @param \AppBundle\Entity\PlaceName $city
     */
    public function removeCity(\AppBundle\Entity\PlaceName $city)
    {
        $this->city->removeElement($city);
    }

    /**
     * Get city
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCity()
    {
        return $this->city;
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
        $this->country[] = $country;

        return $this;
    }

    /**
     * Remove country
     *
     * @param \AppBundle\Entity\PlaceName $country
     */
    public function removeCountry(\AppBundle\Entity\PlaceName $country)
    {
        $this->country->removeElement($country);
    }

    /**
     * Get country
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCountry()
    {
        return $this->country;
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
}
