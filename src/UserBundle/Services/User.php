<?php

namespace UserBundle\Services;

use AppBundle\Entity\CommentLog;
use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Transcript;
use AppBundle\Repository\ResourceRepository;
use AppBundle\Services\Versioning;
use Doctrine\ORM\EntityManager;
use UserBundle\Entity\Access;
use UserBundle\Entity\Preference;

class User
{
    private $em;
    private $versioning;

    public function __construct(EntityManager $em, Versioning $versioning)
    {
        $this->em = $em;
        $this->versioning = $versioning;
    }

    /**
     * @param $user \UserBundle\Entity\User
     * @return Preference
     */
    public function getPreference($user)
    {
        $preference = $this->em->getRepository('UserBundle:Preference')->findOneByUser($user);
        if($preference == null) {$preference = $this->setPreference($user);}
        return $preference;
    }

    /**
     * @param $user \UserBundle\Entity\User
     * @return Preference
     */
    public function setPreference($user) {
        $preference = new Preference();
        $preference->setUser($user);
        $preference->setTranscriptionDeskPosition("leftRead-centerHelp-rightImage");
        $preference->setTutorialStatus("todo");
        $preference->setSmartTEI(true);
        $preference->setShowComplexEntry(true);
        $preference->setCreditActions(true);
        $preference->setNotificationTranscription(true);
        $this->em->persist($preference);
        $this->em->flush();
        return $preference;
    }

    /**
     * @param $user \UserBundle\Entity\User
     * @return Access
     */
    public function getAccess($user)
    {
        $access = $this->em->getRepository('UserBundle:Access')->findOneByUser($user);
        if($access == null) {$access = $this->setAccess($user);}
        return $access;
    }

    /**
     * @param $user \UserBundle\Entity\User
     * @return Access
     */
    public function setAccess($user) {
        $access = new Access();
        $access->setUser($user);
        $access->setIsTaxonomyAccess(false);
        $access->setTaxonomyRequest(null);
        $this->em->persist($access);
        $this->em->flush();
        return $access;
    }

    /**
     * @param $user \UserBundle\Entity\User
     *
     */
    public function getContributions($user) {

    }

    /**
     * @param $user \UserBundle\Entity\User
     * @return Transcript[]
     */
    public function getTranscriptions($user) {
        /** @var $transcripts Transcript[] */
        $transcripts = $this->em->getRepository('AppBundle:Transcript')->findAll();

        /** @var $contributions Transcript[] */
        $contributions = [];
        foreach($transcripts as $transcript) {
            $versions = $this->versioning->getVersions($transcript);
            foreach($versions as $version) {
                if($version->getUsername() == $user->getUsername()) {
                   $contributions[] = $transcript;
                   break;
                }
            }
        }

        return $contributions;
    }

    /**
     * @param $user \UserBundle\Entity\User
     * @return Entity[]
     */
    public function getGroupedTranscriptions($user) {
        /** @var $contributions Transcript[] */
        $contributions = $this->getTranscriptions($user);

        /** @var $resourceRepository ResourceRepository */
        $resourceRepository = $this->em->getRepository('AppBundle:Resource');

        /** @var $entities Entity[] */
        $entities = array();
        /** @var $ids array */
        $ids = array();

        foreach($contributions as $contribution) {
            /** @var $resource Resource */
            $resource = $resourceRepository->findOneBy(array('transcript' => $contribution));
            if($resource && !in_array($resource->getEntity()->getId(), $ids)) {
                    $ids[] = $resource->getEntity()->getId();
                    $entities[] = $resource->getEntity();
            }
        }

        return $entities;
    }

    /**
     * @param $user \UserBundle\Entity\User
     * @return integer
     */
    public function getPrivateMessages($user) {
        $nbUnread = 0;
        $commentLogs = $this->em->getRepository('AppBundle:CommentLog')->findBy(array(), array('id' => 'DESC'));
        /* @var $commentLogs CommentLog[] */

        foreach($commentLogs as $commentLog) {
            $idString = explode('-', $commentLog->getThread()->getId());
            if($idString[0] == 'users' and ($idString[1] == $user->getId() or $idString[2] == $user->getId()) and $commentLog->getIsReadByRecipient() == false and $commentLog->getCreateUser() !== $user) {
                $nbUnread++;
            }
        }

        return $nbUnread;
    }
}