<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Resource;
use AppBundle\Entity\Transcript;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;

class ResourceSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'postPersist',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var $em EntityManager */
        /** @var $transcript Transcript */
        /** @var $resource Resource */

        $resource = $args->getEntity();
        $em = $args->getEntityManager();

        /*if($resource instanceof Resource) {
            $images = explode(', ', $resource->getImages());
            $resource->setImages($images);
        }*/
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        /** @var $em EntityManager */
        /** @var $transcript Transcript */
        /** @var $resource Resource */

        $resource = $args->getEntity();
        $em = $args->getEntityManager();

        /*if($resource instanceof Resource) {
            $transcript = new Transcript();
            $transcript->setStatus("todo");
            $transcript->setResource($resource);
            $em->persist($transcript);
        }*/
    }
}