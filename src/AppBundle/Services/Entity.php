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
     * @throws \Doctrine\ORM\OptimisticLockException
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
     * @var $resource Resource
     */
    public function getStatus($entity) {
        $status = null;
        $listStatus = ["todo" => 0, "transcription" => 0, "validation" => 0, "validated" => 0];

        foreach($entity->getResources() as $resource) {
            if($resource->getTranscript() != null) {
                $listStatus[$resource->getTranscript()->getStatus()] = $listStatus[$resource->getTranscript()->getStatus()]+1;
            }
        }

        if( ($listStatus['transcription'] > 0) or
            ($listStatus['todo'] > 0 and $listStatus['transcription'] == 0 and ($listStatus['validation'] > 0 or $listStatus['validated'] > 0))
        ) {
            $status = "transcription";
        } elseif($listStatus['todo'] > 0 and $listStatus['transcription'] == 0 and $listStatus['validation'] == 0 and $listStatus['validated'] == 0) {
            $status = "todo";
        } elseif($listStatus['todo'] == 0 and $listStatus['transcription'] == 0 and ($listStatus['validation'] > 0 or $listStatus['validated'] > 0)) {
            if($listStatus['validation'] > 0) {
                $status = "validation";
            } elseif($listStatus['validated'] > 0) {
                $status = "validated";
            }
        } else {
            $status = "transcription";
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
                    $arrayContributors[$id]["dates"] = array_merge ($arrayContributors[$id]["dates"], $contributions["dates"]);
                } else {
                    $arrayContributors[$id] = $contributions;
                }
            }
        }
        return $arrayContributors;
    }
}