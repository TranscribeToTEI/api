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
     * @QueryParam(name="entityTypes", nullable=false, description="List of the required entity types, spliced by coma")
     *
     * @Doc\ApiDoc(
     *     section="Logs",
     *     resource=true,
     *     description="Access to logs of T2T",
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
        $loggableEntities = explode(',', $paramFetcher->get('entityTypes'));
        $versions = array();

        foreach($loggableEntities as $entityType) {
            foreach($em->getRepository('AppBundle:'.$entityType)->findAll() as $entity) {
                foreach($this->get('app.versioning')->getVersions($entity) as $version) {
                    $versions[] = ['log' => $version, 'entity' => /*$entity*/null, 'type' => $entityType];
                }
            }
        }

        return $versions;
    }
}
