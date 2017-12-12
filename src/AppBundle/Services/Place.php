<?php

namespace AppBundle\Services;

use AppBundle\Entity\PlaceName;
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
     * @param $property string
     * @return PlaceName[]
     */
    public function getNames($place, $property)
    {
        return $this->em->getRepository("AppBundle:PlaceName")->findBy(array($property => $place), array("createDate" => "DESC"));
    }

    /**
     * @param $place \AppBundle\Entity\Place
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

        return $qb->getQuery()->getResult();
    }
}