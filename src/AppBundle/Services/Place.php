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
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove($place)
    {
        /** @var $testator Testator */
        foreach($this->getTestators($place) as $testator) {
            if($testator->getPlaceOfDeathNormalized() === $place) {
                $testator->setPlaceOfDeathNormalized(null);
            }
            if($testator->getPlaceOfBirthNormalized() === $place) {
                $testator->setPlaceOfBirthNormalized(null);
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
                                  ->where('t.placeOfBirthNormalized = :place')
                                  ->orWhere('t.placeOfDeathNormalized = :place')
                                  ->orWhere('t.addressCity = :place')
                                  ->setParameter('place', $place);

        $list = [];
        foreach($qb->getQuery()->getResult() as $testator) {
            /** @var $testator Testator */
            if($testator->getAddressCity() === $place) {
                $list[] = ['testator' => $testator, 'reason' => 'livedHere'];
            } elseif($testator->getPlaceOfBirthNormalized() === $place) {
                $list[] = ['testator' => $testator, 'reason' => 'bornHere'];
            } elseif($testator->getPlaceOfDeathNormalized() === $place) {
                $list[] = ['testator' => $testator, 'reason' => 'diedHere'];
            }
        }

        return $list;
    }

    /**
     * @param $place \AppBundle\Entity\Place
     * @return array
     */
    public function getWills($place) {
        $repositoryWills = $this->em->getRepository('AppBundle:Will');

        $qb = $repositoryWills->createQueryBuilder('w')
            ->where('w.willWritingPlaceNormalized = :place')
            ->setParameter('place', $place);

        return $qb->getQuery()->getResult();
    }
}