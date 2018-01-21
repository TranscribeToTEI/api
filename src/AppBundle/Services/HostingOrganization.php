<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;

class HostingOrganization
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $hostingOrganization \AppBundle\Entity\HostingOrganization
     * @return mixed
     */
    public function getEntities($hostingOrganization)
    {
        $entities = array();
        $wills =  $this->em->getRepository('AppBundle:Will')->findBy(array('hostingOrganization' => $hostingOrganization));

        foreach ($wills as $will) {
            /** @var $will \AppBundle\Entity\Will */
            $entities[] = $will->getEntity();
        }

        return $entities;
    }
}