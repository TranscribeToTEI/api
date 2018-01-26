<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Transcript;
use AppBundle\Form\EntityType;
use AppBundle\Repository\EntityRepository;
use AppBundle\Repository\TranscriptRepository;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;

class LogsController extends FOSRestController
{
    /**
     * @Rest\Get("/logs")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="entityTypes",  nullable=false, description="List of the required entity types, spliced by coma")
     * @QueryParam(name="getEntity",    nullable=true, description="Should the API return the entity of each log?")
     * @QueryParam(name="idEntity",     nullable=true, description="Returns logs of a specific entity by id")
     * @QueryParam(name="idLog",        nullable=true, description="Returns a specific log entry by id")
     * @QueryParam(name="idVersion",    nullable=true, description="Returns a specific log entry by number of the version")
     * @QueryParam(name="profile",      nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Logs",
     *     resource=true,
     *     description="Access to logs of T2T",
     *     parameters={
     *         { "name"="entityTypes",  "dataType"="string", "description"="List of the required entity types, spliced by coma", "required"=true },
     *         { "name"="getEntity",    "dataType"="boolean", "description"="Should the API return the entity of each log?", "required"=false },
     *         { "name"="idEntity",     "dataType"="integer", "description"="Returns logs of a specific entity by id", "required"=false },
     *         { "name"="idLog",        "dataType"="integer", "description"="Returns a specific log entry by id", "required"=false },
     *         { "name"="idVersion",    "dataType"="integer", "description"="Returns a specific log entry by number of the version", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getLogsAction(Request $request, ParamFetcher $paramFetcher)
    {
        /** @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $loggableEntities =     explode(',', $paramFetcher->get('entityTypes'));
        $getEntity =            $paramFetcher->get('getEntity');
        $specificIdEntity =     $paramFetcher->get('idEntity');
        $specificIdLog =        $paramFetcher->get('idLog');
        $specificIdVersion =    $paramFetcher->get('idVersion');
        $profile =              $paramFetcher->get('profile');
        $versions = array();

        foreach ($loggableEntities as $entityType) {
            if($specificIdLog != '' or ($specificIdVersion != '' and $specificIdEntity != '')) {
                foreach ($em->getRepository('AppBundle:' . $entityType)->findAll() as $entity) {
                    foreach ($this->get('app.versioning')->getVersions($entity) as $version) {
                        if(($specificIdLog != '' and $version->getId() == $specificIdLog) or ($specificIdVersion != '' and $specificIdEntity != '' and $entity->getId() == $specificIdEntity and $version->getVersion() == intval($specificIdVersion))) {
                            if ($version->getObjectClass() === "AppBundle\Entity\Place") {
                                $title = $entity->getNames()[0]->getName();
                            } else {
                                $title = $entity->getName();
                            }

                            if ($getEntity == true) {
                                $versions = ['log' => $version, 'title' => $title, 'entity' => $entity, 'type' => $entityType];
                            } else {
                                $versions = ['log' => $version, 'title' => $title, 'entity' => null, 'type' => $entityType];
                            }
                            break;
                        }
                    }
                }
            } else if($specificIdEntity != '' and $specificIdVersion == '') {
                $entity = $em->getRepository('AppBundle:' . $entityType)->find($specificIdEntity);
                foreach ($this->get('app.versioning')->getVersions($entity) as $version) {
                    if ($version->getObjectClass() === "AppBundle\Entity\Place") {
                        $title = $entity->getNames()[0]->getName();
                    } else {
                        $title = $entity->getName();
                    }

                    if ($getEntity == true) {
                        $versions[] = ['log' => $version, 'title' => $title, 'entity' => $entity, 'type' => $entityType];
                    } else {
                        $versions[] = ['log' => $version, 'title' => $title, 'entity' => null, 'type' => $entityType];
                    }

                }
            } else {
                foreach ($em->getRepository('AppBundle:' . $entityType)->findAll() as $entity) {
                    foreach ($this->get('app.versioning')->getVersions($entity) as $version) {
                        if ($version->getObjectClass() === "AppBundle\Entity\Place") {
                            $title = $entity->getNames()[0]->getName();
                        } else {
                            $title = $entity->getName();
                        }

                        if ($getEntity == true) {
                            $versions[] = ['log' => $version, 'title' => $title, 'entity' => $entity, 'type' => $entityType];
                        } else {
                            $versions[] = ['log' => null, 'logId' => $version->getId(), 'loggedAt' => $version->getLoggedAt(),'title' => $title, 'entity' => null, 'type' => $entityType];
                        }

                    }
                }
            }
        }

        return $versions;
    }
}
