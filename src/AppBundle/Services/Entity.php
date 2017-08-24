<?php

namespace AppBundle\Services;

use AppBundle\Entity\Resource;
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

    /**
     * @param $entity \AppBundle\Entity\Entity
     * @return string
     */
    public function getStatus($entity) {
        $status = null;
        $arrayStatus = [1 => "todo", 2 => "transcription", 3 => "validation", 4 => "validated"];

        /** @var $resource Resource */
        foreach($entity->getResources() as $resource) {
            if($resource->getTranscript() != null) {
                $transcriptStatus = $resource->getTranscript()->getStatus();

                if($status == null) {
                    $status = $transcriptStatus;
                } else {
                    if ($transcriptStatus == "validation" and ($status == "validation" or $status == "validated")) {
                        $status = $transcriptStatus;
                    } elseif ($transcriptStatus == "transcription" and ($status == "transcription" or $status == "validation" or $status == "validated")) {
                        $status = $transcriptStatus;
                    } else {
                        $status = $transcriptStatus;
                    }
                }
            }
        }

        return $status;
    }
}