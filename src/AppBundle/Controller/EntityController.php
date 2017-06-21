<?php

namespace AppBundle\Controller;

use DataBundle\Entity\Entity;

use DataBundle\Form\EntityType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nelmio\ApiDocBundle\Annotation as Doc;

class EntityController extends FOSRestController
{
    /**
     * @Rest\Get("/entities")
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
     *     section="Entities",
     *     resource=true,
     *     description="Get the list of all entities",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getEntitiesAction(Request $request)
    {
        $entities = $this->getDoctrine()->getManager()->getRepository('AppBundle:Entity')->findAll();
        /* @var $entities Entity[] */

        return $entities;
    }

    /**
     * @Rest\Get("/entities/{id}")
     * @Rest\View
     *
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Return one entity",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the entity.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getEntityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Entity')->find($request->get('id'));
        /* @var $entity Entity */

        if (empty($entity)) {
            return new JsonResponse(['message' => 'Entity not found'], Response::HTTP_NOT_FOUND);
        }

        return $entity;
    }

    /**
     * @Rest\Post("/entities")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Create a new entity",
     *     requirements={
     *         {
     *             "name"="will",
     *             "dataType"="entity",
     *             "requirement"="",
     *             "description"="The will of the entity."
     *         },
     *         {
     *             "name"="resources",
     *             "dataType"="array",
     *             "requirement"="",
     *             "description"="The list of resources of the entity."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postEntitiesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Entity();
        $form = $this->createForm(EntityType::class, $entity);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            return $entity;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/entities/{id}")
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Update an existing entity",
     *     requirements={
     *         {
     *             "name"="resources",
     *             "dataType"="array",
     *             "requirement"="",
     *             "description"="The list of resources of the entity."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateEntityAction(Request $request)
    {
        return $this->updateEntity($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/entities/{id}")
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Update an existing entity",
     *     requirements={
     *         {
     *             "name"="resources",
     *             "dataType"="array",
     *             "requirement"="",
     *             "description"="The list of resources of the entity."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchEntityAction(Request $request)
    {
        return $this->updateEntity($request, false);
    }

    private function updateEntity(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Entity')
            ->find($request->get('id'));
        /* @var $entity Entity */
        if (empty($entity)) {
            return new JsonResponse(['message' => 'Entity not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(EntityType::class, $entity);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            return $entity;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/entities/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Remove a entity",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the entity.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function removeEntityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Entity')->find($request->get('id'));
        /* @var $entity Entity */

        if ($entity) {
            $em->remove($entity);
            $em->flush();
        }
    }
}
