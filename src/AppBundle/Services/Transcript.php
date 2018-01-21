<?php

namespace AppBundle\Services;

use AppBundle\Entity\Resource;
use AppBundle\Entity\Testator;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\Gedmo\Loggable\Entity\LogEntry;
use UserBundle\Entity\User;

class Transcript
{
    private $em;
    private $version;

    public function __construct(EntityManager $em, Versioning $version)
    {
        $this->em = $em;
        $this->version = $version;
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
    public function isCurrentlyEdited($transcript)
    {
        /** @var $transcriptLog \AppBundle\Entity\TranscriptLog */
        $transcriptLog = $this->em->getRepository("AppBundle:TranscriptLog")->findOneBy(array("transcript" => $transcript, 'isCurrentlyEdited' => true));

        if($transcriptLog !== null) {
            $datetimeLog = new \DateTime($transcriptLog->getUpdateDate()->format('y-M-d H:i:s'));
            $datetimeNow = new \DateTime('now');
            $interval = $datetimeLog->diff($datetimeNow);

            if(intval($interval->format('%h')) > 1 or intval($interval->format('%d')) > 1 or intval($interval->format('%M')) > 1 or intval($interval->format('%y')) > 1) {
                $transcriptLog->setIsCurrentlyEdited(false);
                $this->em->flush();
                $transcriptLog = null;
            }
        }

        return ($transcriptLog !== null) ? true : false;
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

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @return array
     */
    public function computeVersions($transcript)
    {
        $versions = $this->version->getVersions($transcript);
        $computedVersions = array();

        foreach ($versions as $version) {
            /** @var $version LogEntry */
            /** @var $user User */
            $user = $this->em->getRepository("UserBundle:User")->findOneBy(array("username" => $version->getUsername()));


            $computedVersions[] =
                [
                    "user" => ["id" => $user->getId(), 'name' => $user->getName()],
                    "data" => $version->getData(),
                    "loggedAt" => $version->getLoggedAt(),
                    "id" => $version->getId(),
                    "objectId" => $version->getObjectId(),
                    "objectClass" => $version->getObjectClass(),
                    "version" => $version->getVersion()
                ];
        }

        return $computedVersions;
    }
}