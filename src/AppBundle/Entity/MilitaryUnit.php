<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * MilitaryUnit
 *
 * @ORM\Table(name="militaryUnit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MilitaryUnitRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_military_unit",
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
 *          "update_military_unit",
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
 *          "patch_military_unit",
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
 *          "remove_military_unit",
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
 *     embedded = @Hateoas\Embedded("expr(service('app.militaryUnit').getTestators(object))"),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "content", "taxonomyLinks", "index"}
 *     )
 * )
 */
class MilitaryUnit
{
    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "id", "index"})
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Name of the military unit
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "index", "taxonomyView", "taxonomyList"})
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * Country in which is your military unit
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView"})
     *
     * @var string
     *
     * @Assert\Type("string")
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * Army corps in which is your military unit
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView"})
     *
     * @var string
     *
     * @Assert\Type("string")
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="armyCorps", type="string", length=255, nullable=true)
     */
    private $armyCorps;

    /**
     * Regiment name of your military unit
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView"})
     *
     * @var string
     *
     * @Assert\Type("string")
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="regimentName", type="string", length=255, nullable=true)
     */
    private $regimentName;

    /**
     * Regiment number of your military unit
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "taxonomyView"})
     *
     * @var string
     *
     * @Assert\Type("string")
     *
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="regimentNumber", type="string", length=255, nullable=true)
     */
    private $regimentNumber;

    /**
     * Description of your military unit
     *
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
     * Is the current version of the entity is an official version, provided by the team of the project
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
     * @return MilitaryUnit
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
     * Set description
     *
     * @param string $description
     *
     * @return MilitaryUnit
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
     * @return MilitaryUnit
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
     * @return MilitaryUnit
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
     * @return MilitaryUnit
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
     * @return MilitaryUnit
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
     * @return MilitaryUnit
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
     * Set country
     *
     * @param string $country
     *
     * @return MilitaryUnit
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

    /**
     * Set armyCorps
     *
     * @param string $armyCorps
     *
     * @return MilitaryUnit
     */
    public function setArmyCorps($armyCorps)
    {
        $this->armyCorps = $armyCorps;

        return $this;
    }

    /**
     * Get armyCorps
     *
     * @return string
     */
    public function getArmyCorps()
    {
        return $this->armyCorps;
    }

    /**
     * Set regimentNumber
     *
     * @param string $regimentNumber
     *
     * @return MilitaryUnit
     */
    public function setRegimentNumber($regimentNumber)
    {
        $this->regimentNumber = $regimentNumber;

        return $this;
    }

    /**
     * Get regimentNumber
     *
     * @return string
     */
    public function getRegimentNumber()
    {
        return $this->regimentNumber;
    }

    /**
     * Set isOfficialVersion
     *
     * @param boolean $isOfficialVersion
     *
     * @return MilitaryUnit
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
     * Set regimentName
     *
     * @param string $regimentName
     *
     * @return MilitaryUnit
     */
    public function setRegimentName($regimentName)
    {
        $this->regimentName = $regimentName;

        return $this;
    }

    /**
     * Get regimentName
     *
     * @return string
     */
    public function getRegimentName()
    {
        return $this->regimentName;
    }
}
