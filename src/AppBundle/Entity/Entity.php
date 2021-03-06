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
 * @Hateoas\Relation(
 *     "status",
 *     embedded = @Hateoas\Embedded("expr(service('app.entity').getStatus(object))"),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "content", "search", "listEntities"}
 *     )
 * )
 * @Hateoas\Relation(
 *     "contributors",
 *     embedded = @Hateoas\Embedded("expr(service('app.entity').getContributors(object))"),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "pageEntity"}
 *     )
 * )
 */
class Entity
{
    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "id", "search", "pageEntity", "pageTranscript"})
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The digitization number of your will
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "pageEntity", "pageEdition", "pageTranscript", "iiif", "listEntities", "adminEntity", "adminValidation"})
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="willNumber", type="integer", nullable=true)
     */
    private $willNumber;

    /**
     * The will inside the abstract entity
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "pageEntity", "pageEdition", "pageTranscript", "listEntities", "adminEntity", "adminValidation"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Will", inversedBy="entity", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $will;

    /**
     * The resources inside the abstract entity
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "pageEdition", "pageEntity", "pageTranscript", "iiif", "adminEntity"})
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Resource", mappedBy="entity", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $resources;

    /**
     * Is the entity shown to the users (are they able to see the pictures?)
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "search", "pageEntity", "pageEdition", "pageTranscript", "listEntities", "adminEntity"})
     *
     * @Assert\NotNull()
     * @Assert\Type("bool")
     *
     * @var bool
     *
     * @ORM\Column(name="isShown", type="boolean")
     */
    private $isShown;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata", "adminEntity"})
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
     * @Serializer\Groups({"full", "metadata", "adminEntity"})
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
     * Set isShown
     *
     * @param boolean $isShown
     *
     * @return Entity
     */
    public function setIsShown($isShown)
    {
        $this->isShown = $isShown;

        return $this;
    }

    /**
     * Get isShown
     *
     * @return boolean
     */
    public function getIsShown()
    {
        return $this->isShown;
    }
}
