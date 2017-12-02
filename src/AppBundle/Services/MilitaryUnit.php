<?php

namespace AppBundle\Services;

use AppBundle\Entity\Resource;
use AppBundle\Entity\Testator;
use Doctrine\ORM\EntityManager;

class MilitaryUnit
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $regiment \AppBundle\Entity\MilitaryUnit
     */
    public function remove($regiment)
    {
        /** @var $testator Testator */
        foreach($this->getTestators($regiment) as $testator) {
            $testator->setMilitaryUnitNormalized(null);
        }

        $this->em->remove($regiment);
        $this->em->flush();
    }

    /**
     * @param $militaryUnit \AppBundle\Entity\MilitaryUnit
     * @return array
     */
    public function getTestators($militaryUnit) {
        return $this->em->getRepository('AppBundle:Testator')->findBy(array('militaryUnitNormalized' => $militaryUnit));
    }
}