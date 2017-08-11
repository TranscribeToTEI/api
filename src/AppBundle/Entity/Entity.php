<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

use AppBundle\Entity\Will;
use AppBundle\Entity\Resource;

/**
 * Entity
 *
 * @ORM\Table(name="entity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_entity",
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
 *          "update_entity",
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
 *          "patch_entity",
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
 *          "remove_entity",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class Entity
{
    /**
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="willNumber", type="integer", nullable=true)
     */
    private $willNumber;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Will", inversedBy="entity", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $will;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Resource", mappedBy="entity", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $resources;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="physDescSupport", type="string", length=255, nullable=true)
     */
    private $physDescSupport;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="physDescHeight", type="string", length=255, nullable=true)
     */
    private $physDescHeight;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="physDescWidth", type="string", length=255, nullable=true)
     */
    private $physDescWidth;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     *
     * @ORM\Column(name="physDescHand", type="string", length=255, nullable=true)
     */
    private $physDescHand;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice({"AN", "AD78"})
     *
     * @ORM\Column(name="hostingOrganization", type="string", length=255, nullable=true)
     */
    private $hostingOrganization;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     *
     * @var string
     *
     * @ORM\Column(name="identificationUser", type="string", length=255, nullable=true)
     */
    private $identificationUser;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $createUser;

    /**
     * @Serializer\Since("1.0")
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
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $updateUser;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updateDate", type="datetime", nullable=false)
     */
    protected $updateDate;


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
        $this->resources = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Entity
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
     * Set will
     *
     * @param \AppBundle\Entity\Will $will
     *
     * @return Entity
     */
    public function setWill(\AppBundle\Entity\Will $will = null)
    {
        $this->will = $will;

        return $this;
    }

    /**
     * Get will
     *
     * @return \AppBundle\Entity\Will
     */
    public function getWill()
    {
        return $this->will;
    }

    /**
     * Add resource
     *
     * @param \AppBundle\Entity\Resource $resource
     *
     * @return Entity
     */
    public function addResource(\AppBundle\Entity\Resource $resource)
    {
        $this->resources[] = $resource;

        return $this;
    }

    /**
     * Remove resource
     *
     * @param \AppBundle\Entity\Resource $resource
     */
    public function removeResource(\AppBundle\Entity\Resource $resource)
    {
        $this->resources->removeElement($resource);
    }

    /**
     * Get resources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Set createUser
     *
     * @param \UserBundle\Entity\User $createUser
     *
     * @return Entity
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
     * Set willNumber
     *
     * @param integer $willNumber
     *
     * @return Entity
     */
    public function setWillNumber($willNumber)
    {
        $this->willNumber = $willNumber;

        return $this;
    }

    /**
     * Get willNumber
     *
     * @return integer
     */
    public function getWillNumber()
    {
        return $this->willNumber;
    }

    /**
     * Set identificationUser
     *
     * @param string $identificationUser
     *
     * @return Entity
     */
    public function setIdentificationUser($identificationUser)
    {
        $this->identificationUser = $identificationUser;

        return $this;
    }

    /**
     * Get identificationUser
     *
     * @return string
     */
    public function getIdentificationUser()
    {
        return $this->identificationUser;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Entity
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
     * Set updateUser
     *
     * @param \UserBundle\Entity\User $updateUser
     *
     * @return Entity
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
     * Set hostingOrganization
     *
     * @param string $hostingOrganization
     *
     * @return Entity
     */
    public function setHostingOrganization($hostingOrganization)
    {
        $this->hostingOrganization = $hostingOrganization;

        return $this;
    }

    /**
     * Get hostingOrganization
     *
     * @return string
     */
    public function getHostingOrganization()
    {
        return $this->hostingOrganization;
    }

    /**
     * Set physDescSupport
     *
     * @param string $physDescSupport
     *
     * @return Entity
     */
    public function setPhysDescSupport($physDescSupport)
    {
        $this->physDescSupport = $physDescSupport;

        return $this;
    }

    /**
     * Get physDescSupport
     *
     * @return string
     */
    public function getPhysDescSupport()
    {
        return $this->physDescSupport;
    }

    /**
     * Set physDescHeight
     *
     * @param string $physDescHeight
     *
     * @return Entity
     */
    public function setPhysDescHeight($physDescHeight)
    {
        $this->physDescHeight = $physDescHeight;

        return $this;
    }

    /**
     * Get physDescHeight
     *
     * @return string
     */
    public function getPhysDescHeight()
    {
        return $this->physDescHeight;
    }

    /**
     * Set physDescWidth
     *
     * @param string $physDescWidth
     *
     * @return Entity
     */
    public function setPhysDescWidth($physDescWidth)
    {
        $this->physDescWidth = $physDescWidth;

        return $this;
    }

    /**
     * Get physDescWidth
     *
     * @return string
     */
    public function getPhysDescWidth()
    {
        return $this->physDescWidth;
    }

    /**
     * Set physDescHand
     *
     * @param string $physDescHand
     *
     * @return Entity
     */
    public function setPhysDescHand($physDescHand)
    {
        $this->physDescHand = $physDescHand;

        return $this;
    }

    /**
     * Get physDescHand
     *
     * @return string
     */
    public function getPhysDescHand()
    {
        return $this->physDescHand;
    }
}
