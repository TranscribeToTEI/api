<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Comment\Comment;
use AppBundle\Entity\Comment\Thread;
use AppBundle\Entity\CommentLog;
use AppBundle\Entity\Entity;
use AppBundle\Entity\Place;
use AppBundle\Entity\Resource;
use AppBundle\Entity\TaxonomyVersion;
use AppBundle\Entity\Testator;
use AppBundle\Entity\Transcript;
use AppBundle\Entity\Will;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Psr\Log\LoggerInterface;

class EntitySubscriber implements EventSubscriber
{

    private $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'postUpdate',
            'preRemove'
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /* Testator death place mangement */
        if(get_class($args->getEntity()) == "AppBundle\Entity\Testator") {
            /** @var $em EntityManager */
            /** @var $testator Testator */
            /** @var $resource Resource */

            $testator = $args->getEntity();
            //$testator->setDeathMention('mort pour la France'); -> doit se faire à l'import
        }

        /* Will title management */
        if(get_class($args->getEntity()) == "AppBundle\Entity\Will") {
            /** @var $em EntityManager */
            /** @var $will Will */
            /** @var $resource Resource */

            $will = $args->getEntity();

            $title = "Testament " . $will->getCallNumber() . ", minute du " . $will->getMinuteDateString();

            if ($will->getTestator() != null) {
                $title .= " - " . $will->getTestator()->getName();
            }

            $will->setTitle($title);
        }

        /* Comment Log management */
        if(get_class($args->getEntity()) == "AppBundle\Entity\Comment\Comment") {
            /** @var $em EntityManager */
            /** @var $comment Comment */
            /** @var $commentLog CommentLog */

            $comment = $args->getEntity();

            $commentLog = new CommentLog();
            $commentLog->setThread($comment->getThread());
            $commentLog->setComment($comment);
            $commentLog->setIsReadByAdmin(false);
            $commentLog->setIsReadByRecipient(false);

            if(strpos($comment->getThread()->getId(), 'users') === 0) {
                $commentLog->setIsPrivateThread(true);
            } elseif(strpos($comment->getThread()->getId(), 'users') === false) {
                $commentLog->setIsPrivateThread(false);
            }

            $args->getEntityManager()->persist($commentLog);
        }

    }

    public function preRemove(LifecycleEventArgs $args)
    {
        /* Remove Transcript > Need to remove all the associated logs */
        if(get_class($args->getEntity()) == "AppBundle\Entity\Transcript") {
            /** @var $em EntityManager */
            /** @var $transcript Transcript */

            $transcript = $args->getEntity();
            $logs = $args->getEntityManager()->getRepository('AppBundle:TranscriptLog')->findBy(array('transcript' => $transcript));
            foreach ($logs as $log) {$args->getEntityManager()->remove($log);}
            $args->getEntityManager()->flush();
        }

        if(get_class($args->getEntity()) == "AppBundle\Entity\Resource") {
            /** @var $em EntityManager */
            /** @var $resource Resource */

            $resource = $args->getEntity();
            $transcript = $resource->getTranscript();
            $logs = $args->getEntityManager()->getRepository('AppBundle:TranscriptLog')->findBy(array('transcript' => $transcript));
            foreach ($logs as $log) {$args->getEntityManager()->remove($log);}
        }

    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->postTaxonomyVersion($args->getEntityManager(), $args->getEntity());
    }

    private function postTaxonomyVersion($em, $entity) {
        /* Taxonomy Log management */
        if(get_class($entity) == "AppBundle\Entity\Testator" or get_class($entity) == "AppBundle\Entity\Place" or get_class($entity) == "AppBundle\Entity\MilitaryUnit") {
            /** @var $em EntityManager */
            /** @var $taxonomyLog TaxonomyVersion */

            $logEntries = $em->getRepository('Gedmo\Loggable\Entity\LogEntry')->getLogEntries($entity);
            $version = null;
            foreach ($logEntries as $logEntry) {
                if($version == null) {
                    $version = $logEntry->getVersion();
                }
            }

            $taxonomyLog = new TaxonomyVersion();
            $taxonomyLog->setReviewBy(null);
            $taxonomyLog->setVersionId($version);
            if(get_class($entity) == "AppBundle\Entity\Testator") {
                $taxonomyLog->setTaxonomyType('testator');
                $taxonomyLog->setTestator($entity);
            } elseif(get_class($entity) == "AppBundle\Entity\Place") {
                $taxonomyLog->setTaxonomyType('place');
                $taxonomyLog->setPlace($entity);
            } elseif(get_class($entity) == "AppBundle\Entity\MilitaryUnit") {
                $taxonomyLog->setTaxonomyType('military-unit');
                $taxonomyLog->setMilitaryUnit($entity);
            }

            $em->persist($taxonomyLog);
            $em->flush();
        }

        /* Index name for Places */
        if(get_class($entity) == "AppBundle\Entity\Place") {
            /** @var $em EntityManager */
            /** @var $entity Place */

            $indexName = "";
            if(count($entity->getNames()) > 0) {
                $indexName .= $entity->getNames()[0]->getName();
            }

            $suffixName = "";
            if(count($entity->getFrenchDepartements()) > 0) {
                $suffixName .= $entity->getFrenchDepartements()[0]->getName();
            } elseif(count($entity->getFrenchRegions()) > 0) {
                if($suffixName != "") {$suffixName .= ", ";}
                $suffixName .= $entity->getFrenchRegions()[0]->getName();
            } elseif(count($entity->getCountries()) > 0) {
                if($suffixName != "") {$suffixName .= ", ";}
                $suffixName .= $entity->getCountries()[0]->getName();
            }

            if($suffixName != "") {
                $indexName .= "(".$suffixName.")";
            }

            $entity->setIndexName($indexName);
            $em->persist($entity);
            $em->flush();
        }
    }
}