<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Comment\Thread;
use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Testator;
use AppBundle\Entity\Transcript;
use AppBundle\Entity\Will;
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
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        if(get_class($args->getEntity()) == "AppBundle\Entity\Testator") {
            /** @var $em EntityManager */
            /** @var $testator Testator */
            /** @var $resource Resource */

            $testator = $args->getEntity();
            $testator->setDeathMention('mort pour la France');
        }

        if(get_class($args->getEntity()) == "AppBundle\Entity\Will") {
            /** @var $em EntityManager */
            /** @var $will Will */
            /** @var $resource Resource */

            $will = $args->getEntity();

            $title = "Testament " . $will->getCallNumber() . ", minute du " . $will->getMinuteDate();

            if ($will->getTestator() != null) {
                $title .= " - " . $will->getTestator()->getName();
            }

            $will->setTitle($title);
        }
    }
}