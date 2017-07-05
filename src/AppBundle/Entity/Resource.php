<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

use AppBundle\Entity\Entity;
use JMS\Serializer\Annotation as Serializer;

/**
 * Resource
 *
 * @ORM\Table(name="resource")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResourceRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_resource",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_resource",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "patch",
 *      href = @Hateoas\Route(
 *          "patch_resource",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "remove_resource",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 */
class Resource
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
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="resources")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private $entity;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Expose
     *
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Expose
     *
     * @var int
     *
     * @ORM\Column(name="orderInWill", type="integer")
     */
    private $orderInWill;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Transcript", mappedBy="resource", cascade={"persist", "remove"})
     */
    private $transcript;

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
     * Set type
     *
     * @param string $type
     *
     * @return Resource
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set orderInWill
     *
     * @param integer $orderInWill
     *
     * @return Resource
     */
    public function setOrderInWill($orderInWill)
    {
        $this->orderInWill = $orderInWill;

        return $this;
    }

    /**
     * Get orderInWill
     *
     * @return integer
     */
    public function getOrderInWill()
    {
        return $this->orderInWill;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Resource
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
     * Set entity
     *
     * @param \AppBundle\Entity\Entity $entity
     *
     * @return Resource
     */
    public function setEntity(\AppBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return \AppBundle\Entity\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set createUser
     *
     * @param \UserBundle\Entity\User $createUser
     *
     * @return Resource
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
     * Set transcript
     *
     * @param \AppBundle\Entity\Transcript $transcript
     *
     * @return Resource
     */
    public function setTranscript(\AppBundle\Entity\Transcript $transcript = null)
    {
        $this->transcript = $transcript;

        return $this;
    }

    /**
     * Get transcript
     *
     * @return \AppBundle\Entity\Transcript
     */
    public function getTranscript()
    {
        return $this->transcript;
    }
}
