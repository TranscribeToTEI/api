<?php

namespace AppBundle\Services;

use AppBundle\Entity\Testator;
use Doctrine\ORM\EntityManager;

class Will
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $will \AppBundle\Entity\Will
     */
    public function remove($will)
    {
        $testator = $will->getTestator();
        /** @var $testator Testator */
        $testator->removeWill($will);

        $will->getEntity()->setWill(null);
        $will->setEntity(null);

        $this->em->remove($will);
        $this->em->flush();
    }
}