<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Comment\Thread;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Transcript;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;

class EntitySubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'postFlush',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var $em EntityManager */
        /** @var $transcript Transcript */
        /** @var $resource Resource */

        $entity = $args->getEntity();
        $em = $args->getEntityManager();
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        /*$em = $args->getEntityManager();
        foreach ($em->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Transcript) {
                $thread = new Thread();
                $thread->setCommentable(true);
                $thread->setId('transcript-'.$entity->getId());
                $thread->setPermalink('http://testament-de-poilus.huma-num.fr/thread/transcript-'.$entity->getId());
                $em->persist($thread);
            }
        }
        $em->flush();*/
    }
}