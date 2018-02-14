<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WillType;

use AppBundle\Form\WillTypeType;
use AppBundle\Repository\WillTypeRepository;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nelmio\ApiDocBundle\Annotation as Doc;

class WillTypeController extends FOSRestController
{
    /**
     * @Rest\Get("/will-types")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="search", nullable=true, description="Run a search query in the will types")
     *
     * @Doc\ApiDoc(
     *     section="Will types",
     *     resource=true,
     *     description="Get the list of all will types",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getWillTypesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $search = $paramFetcher->get('search');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:WillType');
        /* @var $repository WillTypeRepository */

        if($search != "") {
            $willTypes = $repository->findBy(array("name" => $search));
            /* @var $willTypes WillType[] */
        } else {
            $willTypes = $repository->findAll();
            /* @var $willTypes WillType[] */
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($willTypes, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id', 'content']))));
    }

    /**
     * @Rest\Get("/will-types/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="WillTypes",
     *     resource=true,
     *     description="Return one will type",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The will type unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getWillTypeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $willType = $em->getRepository('AppBundle:WillType')->find($request->get('id'));
        /* @var $willType WillType */

        if (empty($willType)) {
            return new JsonResponse(['message' => 'WillType not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($willType, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id', 'content']))));
    }

    /**
     * @Rest\Post("/will-types")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="WillTypes",
     *     resource=true,
     *     description="Create a new will type",
     *     input="AppBundle\Form\WillTypeType",
     *     output="AppBundle\Entity\WillType",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postWillTypesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $willType = new WillType();
        $form = $this->createForm(WillTypeType::class, $willType);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($willType);
            $em->flush();
            return $willType;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/will-types/{id}")
     * @Doc\ApiDoc(
     *     section="WillTypes",
     *     resource=true,
     *     description="Update an existing will type",
     *     input="AppBundle\Form\WillTypeType",
     *     output="AppBundle\Entity\WillType",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateWillTypeAction(Request $request)
    {
        return $this->updateWillType($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/will-types/{id}")
     * @Doc\ApiDoc(
     *     section="WillTypes",
     *     resource=true,
     *     description="Update an existing will type",
     *     input="AppBundle\Form\WillTypeType",
     *     output="AppBundle\Entity\WillType",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchWillTypeAction(Request $request)
    {
        return $this->updateWillType($request, false);
    }

    private function updateWillType(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $willType = $em->getRepository('AppBundle:WillType')
            ->find($request->get('id'));
        /* @var $willType WillType */
        if (empty($willType)) {
            return new JsonResponse(['message' => 'WillType not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(WillTypeType::class, $willType);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($willType);
            $em->flush();
            return $willType;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/will-types/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="WillTypes",
     *     resource=true,
     *     description="Remove a will type",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The will type unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function removeWillTypeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $willType = $em->getRepository('AppBundle:WillType')->find($request->get('id'));
        /* @var $willType WillType */

        if ($willType) {
            $em->remove($willType);
            $em->flush();
        }
    }
}
