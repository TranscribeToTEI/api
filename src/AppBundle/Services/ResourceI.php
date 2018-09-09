<?php

namespace AppBundle\Services;

use AppBundle\Entity\Resource;
use AppBundle\Entity\Testator;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use UserBundle\Entity\Preference;
use UserBundle\Entity\User;

class ResourceI
{
    private $em;
    private $transcript;
    private $version;
    private $logger;

    public function __construct(EntityManager $em, Transcript $transcript, Versioning $version, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->transcript = $transcript;
        $this->version = $version;
        $this->logger = $logger;
    }

    /**
     * @param $resource \AppBundle\Entity\Resource
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove($resource)
    {
        $transcript = $resource->getTranscript();
        /** @var $testator Testator */
        $this->transcript->remove($transcript);

        $this->em->remove($resource);
        $this->em->flush();
    }

    /**
     * @param $resource Resource
     * @return mixed
     */
    public function getContributors($resource)
    {
        $arrayContributors = [];
        foreach($this->version->getVersions($resource->getTranscript()) as $version) {
            /** @var $user User */
            $user = $this->em->getRepository('UserBundle:User')->findOneBy(array('username' => $version->getUsername()));

            if($user) {
                /** @var $preference Preference */
                $preference = $this->em->getRepository('UserBundle:Preference')->findOneBy(array('user' => $user));
                if($preference->getCreditActions() == true) {
                    if (array_key_exists($user->getId(), $arrayContributors)) {
                        $arrayContributors[$user->getId()]["contributions"] = $arrayContributors[$user->getId()]["contributions"] + 1;
                        $arrayContributors[$user->getId()]["dates"][] = $version->getLoggedAt();
                    } else {
                        $arrayContributors[$user->getId()] = ["contributions" => 1, "user" => $user, "dates" => [$version->getLoggedAt()]];
                    }
                }
            }

        }

        return $arrayContributors;
    }
}