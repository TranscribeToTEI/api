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

    /**
     * @Rest\Get("/contextual-xml")
     * @Rest\View()
     *
     * @QueryParam(name="id", nullable=false, requirements="\d+", description="Id of the object to submit")
     * @QueryParam(name="type", nullable=false, description="Type of the object to submit")
     *
     * @Doc\ApiDoc(
     *     section="XML",
     *     resource=true,
     *     description="Validate or export transcripts from the API",
     *     parameters={
     *         { "name"="id", "dataType"="integer", "description"="Id of the object to submit", "required"=true },
     *         { "name"="type", "dataType"="string", "description"="Type of the object to submit", "required"=true },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function contextualXmlAction(Request $request, ParamFetcher $paramFetcher)
    {
        /** @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        set_time_limit(0);

        $id_contextualEntity = $paramFetcher->get('id');
        $type_contextualEntity = $paramFetcher->get('type');

        $entity = null;
        switch ($type_contextualEntity) {
            case "place":
                $entity = $em->getRepository('AppBundle:Place')->find($id_contextualEntity);
                break;
            case "militaryUnit":
                $entity = $em->getRepository('AppBundle:MilitaryUnit')->find($id_contextualEntity);
                break;
            case "testator":
                $entity = $em->getRepository('AppBundle:Testator')->find($id_contextualEntity);
                break;
        }

        if($entity == null) {return new JsonResponse(['message' => 'Invalid entity'], Response::HTTP_NOT_FOUND);}

        $result = $this->get('app.xml.builder.contextual.core')->build($entity, true);
        return new JsonResponse(['link' => $this->generateUrl('download_export', array('filename' => $result))], Response::HTTP_CREATED);
    }
}
