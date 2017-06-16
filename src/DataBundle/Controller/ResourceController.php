<?php

namespace DataBundle\Controller;

use DataBundle\Entity\Resource;
use DataBundle\Form\ResourceType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nelmio\ApiDocBundle\Annotation as Doc;

class ResourceController extends FOSRestController
{
    /**
     * @Rest\Get("/resources")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of items per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Get the list of all resources",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getResourcesAction(Request $request)
    {
        $resources = $this->getDoctrine()->getManager()->getRepository('DataBundle:Resource')->findAll();
        /* @var $resources Resource[] */

        return $resources;
    }

    /**
     * @Rest\Get("/resources/{id}")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Return one resource",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the resource.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getResourceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $resource = $em->getRepository('DataBundle:Resource')->find($request->get('id'));
        /* @var $resource Resource */

        if (empty($resource)) {
            return new JsonResponse(['message' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }

        return $resource;
    }

    /**
     * @Rest\Post("/resources")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * 
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Create a new resource",
     *     requirements={
     *         {
     *             "name"="entity",
     *             "dataType"="entity",
     *             "requirement"="",
     *             "description"="The entity aggregating the will."
     *         },
     *         {
     *             "name"="type",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of resource (page, envelope)."
     *         },
     *         {
     *             "name"="order_in_will",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The position of the resource in the order of the resources of the will."
     *         },
     *         {
     *             "name"="transcript",
     *             "dataType"="entity",
     *             "requirement"="",
     *             "description"="The transcript of the resource."
     *         }
     *     },
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
     * @Rest\View()
     * @Rest\Put("/resources/{id}")
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Update an existing resource",
     *     requirements={
     *         {
     *             "name"="type",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of resource (page, envelope)."
     *         },
     *         {
     *             "name"="order_in_will",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The position of the resource in the order of the resources of the will."
     *         }
     *     },
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
     * @Rest\View()
     * @Rest\Patch("/resources/{id}")
     * @Doc\ApiDoc(
     *     section="Resources",
     *     resource=true,
     *     description="Update an existing resource",
     *     requirements={
     *         {
     *             "name"="type",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of resource (page, envelope)."
     *         },
     *         {
     *             "name"="order_in_will",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The position of the resource in the order of the resources of the will."
     *         }
     *     },
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
        $resource = $em->getRepository('DataBundle:Resource')
            ->find($request->get('id'));
        /* @var $resource \DataBundle\Entity\Resource */
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
        $resource = $em->getRepository('DataBundle:Resource')->find($request->get('id'));
        /* @var $resource \DataBundle\Entity\Resource */

        if ($resource) {
            $em->remove($resource);
            $em->flush();
        }
    }
}
