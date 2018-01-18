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
                    } elseif (($transcriptStatus == "transcription" and $status == "todo") or ($transcriptStatus == "todo" and $status == "transcription")) {
                        $status = "transcription";
                    } else {
                        $status = $transcriptStatus;
                    }
                }
            }
        }

        return $status;
    }

    /**
     * @param $entity \AppBundle\Entity\Entity
     * @return mixed
     */
    public function getContributors($entity)
    {
        $arrayContributors = [];
        foreach($entity->getResources() as $resource) {
            $arrayResource = $this->resource->getContributors($resource);

            foreach ($arrayResource as $id=>$contributions) {
                if (array_key_exists($id, $arrayContributors)) {
                    $arrayContributors[$id]["contributions"] = $arrayContributors[$id]["contributions"] + $contributions["contributions"];
                } else {
                    $arrayContributors[$id] = $contributions;
                }
            }
        }
        return $arrayContributors;
    }
}