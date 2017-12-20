<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * PlaceName
 *
 * @ORM\Table(name="place_name")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceNameRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_place_name",
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
 *          "update_place_name",
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
 *          "patch_place_name",
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
 *          "remove_place_name",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class PlaceName
{
    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "id"})
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Related place
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "parent"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place", inversedBy="names")
     * @ORM\JoinColumn(nullable=true)
     */
    private $placeName;

    /**
     * Related departement
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "parent"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place", inversedBy="frenchDepartements")
     * @ORM\JoinColumn(nullable=true)
     */
    private $placeDepartement;

    /**
     * Related region
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "parent"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place", inversedBy="frenchRegions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $placeRegion;

    /**
     * Related city
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "parent"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place", inversedBy="cities")
     * @ORM\JoinColumn(nullable=true)
     */
    private $placeCity;

    /**
     * Related country
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "parent"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place", inversedBy="countries")
     * @ORM\JoinColumn(nullable=true)
     */
    private $placeCountry;

    /**
     * Place name
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search"})
     *
     * @var string
     * @Assert\NotBlank()
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Using starting date of this name
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="date", type="string", length=255, nullable=true)
     */
    private $date;

    /**
     * Using starting year of this name
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
     * @ORM\Column(name="year", type="string", length=5, nullable=true)
     */
    private $year;

    /**
     * Place type when the period of this place name
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @Gedmo\Versioned
     * @Assert\Choice({"Commune", "Localité", "Autre lieu habité", "Forêt", "Colline", "Autre lieu géographique"})
     *
     * @ORM\Column(name="placeType", type="string", length=255, nullable=true)
     */
    private $placeType;

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
     * Set name
     *
     * @param string $name
     *
     * @return PlaceName
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
     * Set date
     *
     * @param string $date
     *
     * @return PlaceName
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set placeType
     *
     * @param string $placeType
     *
     * @return PlaceName
     */
    public function setPlaceType($placeType)
    {
        $this->placeType = $placeType;

        return $this;
    }

    /**
     * Get placeType
     *
     * @return string
     */
    public function getPlaceType()
    {
        return $this->placeType;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return PlaceName
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
     * @return PlaceName
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
     * @return PlaceName
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
     * Set placeName
     *
     * @param \AppBundle\Entity\Place $placeName
     *
     * @return PlaceName
     */
    public function setPlaceName(\AppBundle\Entity\Place $placeName = null)
    {
        $this->placeName = $placeName;

        return $this;
    }

    /**
     * Get placeName
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlaceName()
    {
        return $this->placeName;
    }

    /**
     * Set placeDepartement
     *
     * @param \AppBundle\Entity\Place $placeDepartement
     *
     * @return PlaceName
     */
    public function setPlaceDepartement(\AppBundle\Entity\Place $placeDepartement = null)
    {
        $this->placeDepartement = $placeDepartement;

        return $this;
    }

    /**
     * Get placeDepartement
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlaceDepartement()
    {
        return $this->placeDepartement;
    }

    /**
     * Set placeRegion
     *
     * @param \AppBundle\Entity\Place $placeRegion
     *
     * @return PlaceName
     */
    public function setPlaceRegion(\AppBundle\Entity\Place $placeRegion = null)
    {
        $this->placeRegion = $placeRegion;

        return $this;
    }

    /**
     * Get placeRegion
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlaceRegion()
    {
        return $this->placeRegion;
    }

    /**
     * Set placeCountry
     *
     * @param \AppBundle\Entity\Place $placeCountry
     *
     * @return PlaceName
     */
    public function setPlaceCountry(\AppBundle\Entity\Place $placeCountry = null)
    {
        $this->placeCountry = $placeCountry;

        return $this;
    }

    /**
     * Get placeCountry
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlaceCountry()
    {
        return $this->placeCountry;
    }

    /**
     * Set createUser
     *
     * @param \UserBundle\Entity\User $createUser
     *
     * @return PlaceName
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
     * @return PlaceName
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
     * Set year
     *
     * @param string $year
     *
     * @return PlaceName
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set placeCity
     *
     * @param \AppBundle\Entity\Place $placeCity
     *
     * @return PlaceName
     */
    public function setPlaceCity(\AppBundle\Entity\Place $placeCity = null)
    {
        $this->placeCity = $placeCity;

        return $this;
    }

    /**
     * Get placeCity
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlaceCity()
    {
        return $this->placeCity;
    }
}
