<?php

namespace AppBundle\Services;

use AppBundle\Entity\Comment\Thread;
use AppBundle\Entity\Resource;
use Doctrine\ORM\EntityManager;

class CommentLog
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $commentLog \AppBundle\Entity\CommentLog
     * @return array
     */
    public function getContentInfo($commentLog) {
        $info = [];
        $thread = $commentLog->getThread();
        $explode = explode('-', $thread->getId());
        if(strpos($thread->getId(), 'content') !== false) {
            $info["type"] = "content";
            $info["id"] = $explode[count($explode)-1];
        } elseif(strpos($thread->getId(), 'transcript') !== false) {
            $info["type"] = "edition";
            $info["id"] = $explode[count($explode)-1];

            $edition = $this->em->getRepository('AppBundle:Resource')->find($explode[count($explode)-1]);
            if($edition != null) {
                $info['entity'] = $edition->getEntity()->getId();
            }
        } elseif(strpos($thread->getId(), 'entity') !== false) {
            $info["type"] = "entity";
            $info["id"] = $explode[count($explode)-1];
        } elseif(strpos($thread->getId(), 'users') !== false) {
            $info["type"] = "user";
            $info["id-1"] = $explode[count($explode)-2];
            $info["id-2"] = $explode[count($explode)-1];
        } elseif(strpos($thread->getId(), 'military-units') !== false) {
            $info["type"] = "military-unit";
            $info["id"] = $explode[count($explode)-1];
        } elseif(strpos($thread->getId(), 'places') !== false) {
            $info["type"] = "place";
            $info["id"] = $explode[count($explode)-1];
        } elseif(strpos($thread->getId(), 'testators') !== false) {
            $info["type"] = "testator";
            $info["id"] = $explode[count($explode)-1];
        } elseif(strpos($thread->getId(), 'trainingContent') !== false) {
            $info["type"] = "trainingContent";
            $info["id"] = $explode[count($explode)-1];
        }

        return $info;
    }
}