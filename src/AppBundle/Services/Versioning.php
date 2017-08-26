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
     * @param mixed
     * @return mixed
     */
    public function getVersions($entity)
    {
        $repo = $this->em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        return $repo->getLogEntries($entity);
    }
}