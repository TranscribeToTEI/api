<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Gedmo\Loggable\Entity\LogEntry;

class Versioning
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @return mixed
     */
    public function getVersions($transcript)
    {
        $repo = $this->em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        return $repo->getLogEntries($transcript);
    }
}