<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entity;

use AppBundle\Entity\Resource;
use AppBundle\Form\EntityType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class EntityController extends FOSRestController
{
    /**
     * @Rest\Get("/entities")
     * @Rest\View(serializerGroups={"id", "content"}, serializerEnableMaxDepthChecks=true)
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
        /* @var $entities \AppBundle\Entity\Entity[] */

        return $entities;
    }

    /**
     * @Rest\Get("/entities/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
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
     * @Security("is_granted('ROLE_MODO')")
     */
    public function postEntitiesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Entity();
        $form = $this->createForm(EntityType::class, $entity);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($entity);
            foreach($entity->getResources() as $resource) {
                /** @var $resource Resource */
                $resource->setEntity($entity);
            }
            $entity->getWill()->setEntity($entity);
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
            foreach($entity->getResources() as $resource) {
                /** @var $resource Resource */
                $resource->setEntity($entity);
            }
            $em->merge($entity);
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
            $entity_service = $this->get('app.entity');
            /** @var $entity_service \AppBundle\Services\Entity */
            $entity_service->remove($entity);
        }

    }
}
