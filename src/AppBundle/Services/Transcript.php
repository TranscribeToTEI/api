<?php

namespace AppBundle\Services;

use AppBundle\Entity\Resource;
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
        /** @var $resource \AppBundle\Entity\Resource */
        $resource = $this->em->getRepository("AppBundle:Resource")->findOneBy(array("transcript" => $transcript));
        $resource->setTranscript(null);

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

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @return bool
     */
    public function isOpened($transcript)
    {
        /** @var $resource \AppBundle\Entity\Resource */
        $transcriptLog = $this->em->getRepository("AppBundle:TranscriptLog")->findOneBy(array("transcript" => $transcript, 'isOpened' => false));

        return ($transcriptLog !== null) ? false : true;
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @return array
     */
    public function getLogs($transcript)
    {
        /** @var $resource \AppBundle\Entity\Resource */
        return $this->em->getRepository("AppBundle:TranscriptLog")->findBy(array("transcript" => $transcript));
    }
}