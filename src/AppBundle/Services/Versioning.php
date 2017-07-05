<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class Versioning
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getVersions($id)
    {
        $transcript = $this->em->getRepository('AppBundle:Transcript')->find($id);
        $repo = $this->em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        return $repo->getLogEntries($transcript);
    }
}