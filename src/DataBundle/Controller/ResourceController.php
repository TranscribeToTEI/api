<?php

namespace DataBundle\Controller;

use DataBundle\Entity\Resource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Nelmio\ApiDocBundle\Annotation as Doc;

class ResourceController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/resources/{id}",
     *     name = "data_resource_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 200)
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
    public function showAction(\DataBundle\Entity\Resource $resource)
    {
        return $resource;
    }

    /**
     * @Rest\Get(
     *    path = "/resources",
     *    name = "data_resource_list"
     * )
     * @Rest\View(StatusCode = 200)
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
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository("DataBundle:Resource")->findAll();
    }

    /**
     * @Rest\Post(
     *    path = "/resources",
     *    name = "data_resource_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("resource", converter="fos_rest.request_body")
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
    public function createAction(\DataBundle\Entity\Resource $resource)
    {
        //dump($resource); die;
        $em = $this->getDoctrine()->getManager();
        $em->persist($resource);
        $em->flush();

        return $this->view($resource, Response::HTTP_CREATED, ['Location' => $this->generateUrl('data_resource_show', ['id' => $resource->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]);
    }

    /**
     * @Rest\Put(
     *    path = "/resources/{id}",
     *    name = "data_resource_update",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200)
     * @ParamConverter("newResource", converter="fos_rest.request_body")
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
    public function updateAction(\DataBundle\Entity\Resource $resource, \DataBundle\Entity\Resource $newResource)
    {
        $resource->setType($newResource->getType());
        $resource->setOrderInWill($newResource->getOrderInWill());

        $this->getDoctrine()->getManager()->flush();

        return $resource;
    }

    /**
     * @Rest\Delete(
     *     path = "/resources/{id}",
     *     name = "data_resource_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 204)
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
    public function deleteAction(\DataBundle\Entity\Resource $resource)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($resource);
        $em->flush();

        return;
    }
}
