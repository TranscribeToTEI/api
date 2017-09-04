<?php

namespace AppBundle\Services;

use AppBundle\Entity\Resource;
use AppBundle\Entity\Testator;
use Doctrine\ORM\EntityManager;

class Place
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $place \AppBundle\Entity\Place
     */
    public function remove($place)
    {
        /** @var $testator Testator */
        foreach($this->getTestators($place) as $testator) {
            if($testator->getPlaceOfDeath() === $place) {
                $testator->setPlaceOfDeath(null);
            }
            if($testator->getPlaceOfBirth() === $place) {
                $testator->setPlaceOfBirth(null);
            }
        }

        $this->em->remove($place);
        $this->em->flush();
    }

    /**
     * @param $place \AppBundle\Entity\Place
     * @return array
     */
    public function getTestators($place) {
        $repositoryTestators = $this->em->getRepository('AppBundle:Testator');

        $qb = $repositoryTestators->createQueryBuilder('t')
                                  ->where('t.placeOfBirth = :place')
                                  ->orWhere('t.placeOfDeath = :place')
                                  ->setParameter('place', $place);

        return $qb->getQuery()->getResult();
    }
}