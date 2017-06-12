<?php

namespace DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use DataBundle\Entity\Will;
use DataBundle\Entity\Resource;

/**
 * Entity
 *
 * @ORM\Table(name="entity")
 * @ORM\Entity(repositoryClass="DataBundle\Repository\EntityRepository")
 */
class Entity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Will", inversedBy="entity")
     * @ORM\JoinColumn(name="will_id", referencedColumnName="id")
     */
    private $will;

    /**
     * @ORM\OneToMany(targetEntity="Resource", mappedBy="entity")
     */
    private $resources;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $createUser;

    /**
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
     * @param \DataBundle\Entity\Will $will
     *
     * @return Entity
     */
    public function setWill(\DataBundle\Entity\Will $will = null)
    {
        $this->will = $will;

        return $this;
    }

    /**
     * Get will
     *
     * @return \DataBundle\Entity\Will
     */
    public function getWill()
    {
        return $this->will;
    }

    /**
     * Add resource
     *
     * @param \DataBundle\Entity\Resource $resource
     *
     * @return Entity
     */
    public function addResource(\DataBundle\Entity\Resource $resource)
    {
        $this->resources[] = $resource;

        return $this;
    }

    /**
     * Remove resource
     *
     * @param \DataBundle\Entity\Resource $resource
     */
    public function removeResource(\DataBundle\Entity\Resource $resource)
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
