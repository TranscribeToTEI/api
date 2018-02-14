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
     * @QueryParam(name="id", nullable=false, requirements="\d+", description="Id of the object to submit")
     *
     * @Doc\ApiDoc(
     *     section="XML",
     *     resource=true,
     *     description="Validate or export transcripts from the API",
     *     parameters={
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
        /** @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        set_time_limit(0);

        $entity = $em->getRepository('AppBundle:Entity')->find($paramFetcher->get('id'));
        if($entity == null) {return new JsonResponse(['message' => 'Invalid entity'], Response::HTTP_NOT_FOUND);}

        $result = $this->get('app.xml.builder.core')->build($entity, true);
        return new JsonResponse(['link' => $this->generateUrl('download_export', array('filename' => $result))], Response::HTTP_CREATED);
    }
}
