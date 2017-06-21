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
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_entity",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "patch",
 *      href = @Hateoas\Route(
 *          "patch_entity",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "remove_entity",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 */
class Entity
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
     * @ORM\OneToOne(targetEntity="Will", inversedBy="entity", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="will_id", referencedColumnName="id", nullable=true)
     */
    private $will;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @ORM\OneToMany(targetEntity="Resource", mappedBy="entity", cascade={"persist", "remove"})
     */
    private $resources;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
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
}
