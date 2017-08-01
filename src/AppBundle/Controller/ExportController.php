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

class ExportController extends FOSRestController
{
    /**
     * @Rest\Get("/export")
     * @Rest\View()
     *
     * @QueryParam(name="type", nullable=false, requirements="entity|transcript", description="Type of the object to export")
     * @QueryParam(name="id", nullable=false, requirements="\d+", description="Id of the object to export")
     *
     * @Doc\ApiDoc(
     *     section="Export",
     *     resource=true,
     *     description="Export transcripts from the API",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function buildExportAction(Request $request, ParamFetcher $paramFetcher)
    {
        $type = $paramFetcher->get('type');
        $id = $paramFetcher->get('id');
        if(empty($id)) {return new JsonResponse(['message' => 'Invalid object\'s id'], Response::HTTP_NOT_ACCEPTABLE);}

        $em = $this->getDoctrine()->getManager();
        /** @var $em EntityManager */

        if($type == 'entity') {
            $repository = $em->getRepository('AppBundle:Entity');
            /** @var $repository EntityRepository */
            $entity = $repository->find($id);
            /** @var $entity Entity */
            if($entity == null) {return new JsonResponse(['message' => 'Invalid entity'], Response::HTTP_NOT_FOUND);}

            $filename = $this->get('app.xml')->build($entity);
            return new JsonResponse(['link' => $this->generateUrl('download_export', array('filename' => $filename))], Response::HTTP_CREATED);
        } elseif($type == 'transcript') {
            $repository = $em->getRepository('AppBundle:Transcript');
            /** @var $repository TranscriptRepository */
            $transcript = $repository->find($id);
            /** @var $transcript Transcript */

            if ($transcript != null && $transcript->getContent() != null) {
                $filename = $this->get('app.xml')->build($this->get('app.transcript')->getResource()->getEntity(), $transcript);
                return new JsonResponse(['link' => $this->generateUrl('download_export', array('filename' => $filename))], Response::HTTP_CREATED);
            } else {
                return new JsonResponse(['message' => 'Invalid transcript'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return new JsonResponse(['message' => 'Invalid object\'s type'], Response::HTTP_NOT_ACCEPTABLE);
        }
    }
}
