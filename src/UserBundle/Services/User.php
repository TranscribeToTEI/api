<?php

namespace UserBundle\Services;

use Doctrine\ORM\EntityManager;
use UserBundle\Entity\Preference;

class User
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getPreference($user)
    {
        $preference = $this->em->getRepository('UserBundle:Preference')->findOneByUser($user);
        if($preference == null) {$preference = $this->setPreference($user);}
        return $preference;
    }

    public function setPreference($user) {
        $preference = new Preference();
        $preference->setUser($user);
        $preference->setTranscriptionDeskPosition("readLeft");
        $this->em->persist($preference);
        $this->em->flush();
        return $preference;
    }
}