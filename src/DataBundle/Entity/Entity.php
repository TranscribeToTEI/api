<?php

namespace DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var \stdClass
     *
     * @ORM\Column(name="will", type="object")
     */
    private $will;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="resources", type="object")
     */
    private $resources;


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
     * Set will
     *
     * @param \stdClass $will
     *
     * @return Entity
     */
    public function setWill($will)
    {
        $this->will = $will;

        return $this;
    }

    /**
     * Get will
     *
     * @return \stdClass
     */
    public function getWill()
    {
        return $this->will;
    }

    /**
     * Set resources
     *
     * @param \stdClass $resources
     *
     * @return Entity
     */
    public function setResources($resources)
    {
        $this->resources = $resources;

        return $this;
    }

    /**
     * Get resources
     *
     * @return \stdClass
     */
    public function getResources()
    {
        return $this->resources;
    }
}

