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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;

class XMLController extends FOSRestController
{
    /**
     * @Rest\Get("/xml")
     * @Rest\View()
     *
     * @QueryParam(name="context", nullable=false, requirements="export|validate", description="Context of the request")
     * @QueryParam(name="type", nullable=false, requirements="entity|transcript", description="Type of the object to submit")
     * @QueryParam(name="id", nullable=false, requirements="\d+", description="Id of the object to submit")
     *
     * @Doc\ApiDoc(
     *     section="XML",
     *     resource=true,
     *     description="Validate or export transcripts from the API",
     *     parameters={
     *         { "name"="context", "dataType"="string", "description"="Context of the request", "required"=true },
     *         { "name"="type", "dataType"="string", "description"="Type of the object to submit", "required"=true },
     *         { "name"="id", "dataType"="integer", "description"="Id of the object to submit", "required"=true },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function xmlAction(Request $request, ParamFetcher $paramFetcher)
    {
        set_time_limit(0);

        $type = $paramFetcher->get('type');
        $id = $paramFetcher->get('id');
        $context = $paramFetcher->get('context');
        if(empty($id)) {return new JsonResponse(['message' => 'Invalid object\'s id'], Response::HTTP_NOT_ACCEPTABLE);}
        if(empty($type) OR ($type != "entity" AND $type != "transcript")) {return new JsonResponse(['message' => 'Invalid object\'s type'], Response::HTTP_NOT_ACCEPTABLE);}
        if(empty($context) OR ($context != "validate" AND $context != "export")) {return new JsonResponse(['message' => 'Invalid context'], Response::HTTP_NOT_ACCEPTABLE);}

        $em = $this->getDoctrine()->getManager();
        /** @var $em EntityManager */

        if($type == 'entity') {
            $repository = $em->getRepository('AppBundle:Entity');
            /** @var $repository EntityRepository */
            $entity = $repository->find($id);
            /** @var $entity Entity */
            if($entity == null) {return new JsonResponse(['message' => 'Invalid entity'], Response::HTTP_NOT_FOUND);}
            $transcript = null;
        } elseif($type == 'transcript') {
            $repository = $em->getRepository('AppBundle:Transcript');
            /** @var $repository TranscriptRepository */
            $transcript = $repository->find($id);
            /** @var $transcript Transcript */
            if ($transcript == null or $transcript->getContent() == null) {return new JsonResponse(['message' => 'Invalid transcript'], Response::HTTP_NOT_FOUND);}
            $entity = null;
        } else {return new JsonResponse(['message' => 'Invalid object\'s type'], Response::HTTP_NOT_ACCEPTABLE);}

        if($context == "export") {
            return $this->export($entity, $transcript);
        } elseif($context == "validate") {
            return $this->validate($entity, $transcript);
        } else {
            return new JsonResponse(['message' => 'Invalid context'], Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function export($entity, $transcript)
    {
        $result = $this->get('app.xml.builder')->build($entity, $transcript, true);
        return new JsonResponse(['link' => $this->generateUrl('download_export', array('filename' => $result))], Response::HTTP_CREATED);
    }

    public function validate($entity, $transcript)
    {
        $result = $this->get('app.xml.validator')->validate($entity, $transcript);
        return new JsonResponse(['message' => $result['validation'], 'content' => $result['doc']], Response::HTTP_OK);
    }
}
