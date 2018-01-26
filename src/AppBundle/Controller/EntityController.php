<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entity;

use AppBundle\Entity\Resource;
use AppBundle\Form\EntityType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class EntityController extends FOSRestController
{
    /**
     * @Rest\Get("/entities")
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     * @QueryParam(name="search",   nullable=true, description="Run a search query in the entities")
     *
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Get the list of all entities",
     *     parameters={
     *         { "name"="profile",  "dataType"="string", "description"="Search profile to apply", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getEntitiesAction(Request $request, ParamFetcher $paramFetcher)
    {
        /* @var $entities Entity[] */
        $search = $paramFetcher->get('search');

        if($search != "") {
            $searchInfo = explode(';', $search);
            $repositoryEntities = $this->getDoctrine()->getManager()->getRepository('AppBundle:Entity');

            $qb = $repositoryEntities->createQueryBuilder('e')
                ->join('e.will', 'w')
                ->leftJoin('w.hostingOrganization', 'h')
                ->where('e.willNumber = :willNumber')
                ->andWhere('h.code = :code')
                ->setParameters(array('willNumber' => $searchInfo[0], 'code' => $searchInfo[1]));

            $entities = $qb->getQuery()->getResult();
        } else {
            $entities = $this->getDoctrine()->getManager()->getRepository('AppBundle:Entity')->findAll();
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "content"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($entities, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/entities/{id}")
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
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
    public function getEntityAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Entity')->find($request->get('id'));
        /* @var $entity Entity */

        if (empty($entity)) {
            return new JsonResponse(['message' => 'Entity not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["full"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($entity, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Post("/entities")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Create a new entity",
     *     input="AppBundle\Form\EntityType",
     *     output="AppBundle\Entity\Entity",
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
            return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($entity, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id']))));
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/entities/{id}")
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Update an existing entity",
     *     input="AppBundle\Form\EntityType",
     *     output="AppBundle\Entity\Entity",
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
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/entities/{id}")
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Update an existing entity",
     *     input="AppBundle\Form\EntityType",
     *     output="AppBundle\Entity\Entity",
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
            return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($entity, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id']))));
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
