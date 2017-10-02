<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Comment\Thread;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Transcript;
use AppBundle\Entity\Will;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;

class WillSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        if(get_class($args->getEntity()) == "Will") {
            /** @var $em EntityManager */
            /** @var $will Will */
            /** @var $resource Resource */

            $will = $args->getEntity();
            $em = $args->getEntityManager();

            $title = "Testament " . $will->getCallNumber() . ", minute du " . $will->getMinuteDate();

            if ($will->getTestator() != null) {
                $title .= " - " . $will->getTestator()->getName();
            }

            $will->setTitle($title);
        }
    }
}