<?php

namespace AppBundle\Services;

use AppBundle\Entity\Resource;
use AppBundle\Entity\Testator;
use Doctrine\ORM\EntityManager;

class Regiment
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $regiment \AppBundle\Entity\Regiment
     */
    public function remove($regiment)
    {
        /** @var $testator Testator */
        foreach($this->getTestators($regiment) as $testator) {
            $testator->setRegiment(null);
        }

        $this->em->remove($regiment);
        $this->em->flush();
    }

    /**
     * @param $regiment \AppBundle\Entity\Regiment
     * @return array
     */
    public function getTestators($regiment) {
        return $this->em->getRepository('AppBundle:Testator')->findBy(array('regiment' => $regiment));
    }
}