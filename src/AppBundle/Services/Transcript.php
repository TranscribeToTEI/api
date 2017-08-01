<?php

namespace AppBundle\Services;

use AppBundle\Entity\Testator;
use Doctrine\ORM\EntityManager;

class Transcript
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     */
    public function remove($transcript)
    {
        $this->em->remove($transcript);
        $this->em->flush();
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @return null|object \AppBundle\Entity\Resource
     */
    public function getResource($transcript)
    {
        return $this->em->getRepository('AppBundle:Resource')->findOneBy(array('transcript' => $transcript));
    }
}