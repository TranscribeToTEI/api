<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Resource;
use AppBundle\Entity\Transcript;
use AppBundle\Form\ResourceType;
use AppBundle\Repository\ResourceRepository;
use Doctrine\ORM\EntityManager;
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

class ResourceController extends FOSRestController
{
    /**
     * @Rest\Get("/resources")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     * @QueryParam(name="transcript", nullable=true, description="Identifier of the transcript related to the resource.")
     *
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Get the list of all resources",
     *     parameters={
     *         { "name"="transcript", "dataType"="integer", "description"="Identifier of the transcript related to the resource.", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getResourcesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $transcript_id = $paramFetcher->get('transcript');

        $em = $this->getDoctrine()->getManager();
        /** @var $em EntityManager */

        $repository = $em->getRepository('AppBundle:Resource');
        /* @var $repository ResourceRepository */

        if($transcript_id != "") {
            $transcript = $this->getDoctrine()->getManager()->getRepository('AppBundle:Transcript')->find($transcript_id);
            /* @var $transcript Transcript */
            $resources = $repository->findOneBy(array("transcript" => $transcript));
        } else {
            $resources = $repository->findAll();
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "pageEdition"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($resources, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/resources/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Return one resource",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getResourceAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $resource = $em->getRepository('AppBundle:Resource')->find($request->get('id'));
        /* @var $resource Resource */

        if (empty($resource)) {
            return new JsonResponse(['message' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "pageEdition", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($resource, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Post("/resources")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     * 
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Create a new resource",
     *     input="AppBundle\Form\ResourceType",
     *     output="AppBundle\Entity\Resource",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postResourcesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $resource = new Resource();
        $form = $this->createForm(ResourceType::class, $resource);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($resource);
            $em->flush();
            return $resource;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/resources/{id}")
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Update an existing resource",
     *     input="AppBundle\Form\ResourceType",
     *     output="AppBundle\Entity\Resource",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateResourceAction(Request $request)
    {
        return $this->updateResource($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/resources/{id}")
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Update an existing resource",
     *     input="AppBundle\Form\ResourceType",
     *     output="AppBundle\Entity\Resource",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchResourceAction(Request $request)
    {
        return $this->updateResource($request, false);
    }

    private function updateResource(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $resource = $em->getRepository('AppBundle:Resource')
            ->find($request->get('id'));
        /* @var $resource \AppBundle\Entity\Resource */
        if (empty($resource)) {
            return new JsonResponse(['message' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(ResourceType::class, $resource);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->persist($resource);
            $em->flush();
            return $resource;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/resources/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     *
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Remove a resource",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the resource.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function removeResourceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $resource = $em->getRepository('AppBundle:Resource')->find($request->get('id'));
        /* @var $resource \AppBundle\Entity\Resource */

        if ($resource) {
            $em->remove($resource);
            $em->flush();
        }
    }
}
