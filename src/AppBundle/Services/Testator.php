<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;

class Testator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $testator \AppBundle\Entity\Testator
     * @return mixed
     */
    public function getWills($testator)
    {
        return $this->em->getRepository('AppBundle:Will')->findBy(array('testator' => $testator));
    }
}