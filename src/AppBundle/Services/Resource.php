<?php

namespace AppBundle\Services;

use AppBundle\Entity\Testator;
use Doctrine\ORM\EntityManager;

class Resource
{
    private $em;
    private $transcript;

    public function __construct(EntityManager $em, Transcript $transcript)
    {
        $this->em = $em;
        $this->transcript = $transcript;
    }

    /**
     * @param $resource \AppBundle\Entity\Resource
     */
    public function remove($resource)
    {
        $transcript = $resource->getTranscript();
        /** @var $testator Testator */
        $this->transcript->remove($transcript);

        $this->em->remove($resource);
        $this->em->flush();
    }
}