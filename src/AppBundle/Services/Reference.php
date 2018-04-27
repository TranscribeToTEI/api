<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class Reference
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getReferenceItem($itemOriginType, $item) {
        $referenceItemRepository = $this->em->getRepository('AppBundle:ReferenceItem');

        if($itemOriginType == 'printedReference') {
            return $referenceItemRepository->findOneBy(array('printedReference' => $item->getId()));
        } elseif ($itemOriginType == 'manuscriptReference') {
            return $referenceItemRepository->findOneBy(array('manuscriptReference' => $item->getId()));
        } elseif ($itemOriginType == 'freeReference') {
            return $item;
        }
    }
}