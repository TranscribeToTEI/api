<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class Entity
{
    private $em;
    private $will;
    private $resource;

    public function __construct(EntityManager $em, Will $will, ResourceI $resource)
    {
        $this->em = $em;
        $this->will = $will;
        $this->resource = $resource;
    }

    /**
     * @param $entity \AppBundle\Entity\Entity
     */
    public function remove($entity)
    {
        if($entity->getWill() != null) {
            $this->will->remove($entity->getWill());
        }
        foreach($entity->getResources() as $resource) {
            $this->resource->remove($resource);
        }

        $this->em->remove($entity);
        $this->em->flush();
    }
}