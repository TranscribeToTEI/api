<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\ReferenceItem;

use AppBundle\Form\ReferenceItemType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ReferenceItemController extends FOSRestController
{
    /**
     * @Rest\Get("/reference-items")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="entity",           nullable=true, description="Gets all bibliographical elements for an entity")
     * @QueryParam(name="testator",         nullable=true, description="Gets all bibliographical elements for a testator")
     * @QueryParam(name="place",            nullable=true, description="Gets all bibliographical elements for a place")
     * @QueryParam(name="military-unit",    nullable=true, description="Gets all bibliographical elements for a military unit")
     * @QueryParam(name="profile",          nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="ReferenceItems",
     *     resource=true,
     *     description="Get the list of all reference items",
     *     parameters={
     *         { "name"="entity", "dataType"="string", "description"="Gets all bibliographical elements for an entity", "required"=false },
     *         { "name"="testator", "dataType"="string", "description"="Gets all bibliographical elements for a testator", "required"=false },
     *         { "name"="place", "dataType"="string", "description"="Gets all bibliographical elements for a place", "required"=false },
     *         { "name"="military-unit", "dataType"="string", "description"="Gets all bibliographical elements for a military unit", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getReferenceItemsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $id_entity = $paramFetcher->get('entity');
        $id_testator = $paramFetcher->get('testator');
        $id_place = $paramFetcher->get('place');
        $id_militaryUnit = $paramFetcher->get('military-unit');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:ReferenceItem');
        /* @var $repository EntityRepository */

        if($id_entity != "" or $id_testator != "" or $id_place != "" or $id_militaryUnit != "") {
            if($id_entity != "") {
                $abstractEntity = "entity";
                $entity = $this->getDoctrine()->getManager()->getRepository('AppBundle:Entity')->findOneById($id_entity);
            } elseif($id_testator != "") {
                $abstractEntity = "testator";
                $entity = $this->getDoctrine()->getManager()->getRepository('AppBundle:Testator')->findOneById($id_testator);
            } elseif($id_place != "") {
                $abstractEntity = "place";
                $entity = $this->getDoctrine()->getManager()->getRepository('AppBundle:Entity')->findOneById($id_place);
            } elseif($id_militaryUnit != "") {
                $abstractEntity = "militaryUnit";
                $entity = $this->getDoctrine()->getManager()->getRepository('AppBundle:MilitaryUnit')->findOneById($id_militaryUnit);
            } else {
                $abstractEntity = null;
                $entity = null;
            }

            if($entity != null) {
                $referenceItems = $repository->findBy(array($abstractEntity => $entity));
                /* @var $referenceItems ReferenceItem[] */
            } else {
                $referenceItems = null;
            }
        } else {
            $referenceItems = $repository->findAll();
            /* @var $referenceItems ReferenceItem[] */
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "bibliography", "bibliographySubject"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($referenceItems, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/reference-items/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",          nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="ReferenceItems",
     *     resource=true,
     *     description="Return one reference item",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The reference item unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getReferenceItemAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $referenceItem = $em->getRepository('AppBundle:ReferenceItem')->find($request->get('id'));
        /* @var $referenceItem ReferenceItem */

        if (empty($referenceItem)) {
            return new JsonResponse(['message' => 'ReferenceItem not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "bibliography", "bibliographySubject", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($referenceItem, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Post("/reference-items")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="ReferenceItems",
     *     resource=true,
     *     description="Create a new reference item",
     *     input="AppBundle\Form\ReferenceItemType",
     *     output="AppBundle\Entity\ReferenceItem",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function postReferenceItemsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $referenceItem = new ReferenceItem();
        $form = $this->createForm(ReferenceItemType::class, $referenceItem);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($referenceItem);
            $em->flush();
            return $referenceItem;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/reference-items/{id}")
     * @Doc\ApiDoc(
     *     section="ReferenceItems",
     *     resource=true,
     *     description="Update an existing reference item",
     *     input="AppBundle\Form\ReferenceItemType",
     *     output="AppBundle\Entity\ReferenceItem",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updateReferenceItemAction(Request $request)
    {
        return $this->updateReferenceItem($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/reference-items/{id}")
     * @Doc\ApiDoc(
     *     section="ReferenceItems",
     *     resource=true,
     *     description="Update an existing reference item",
     *     input="AppBundle\Form\ReferenceItemType",
     *     output="AppBundle\Entity\ReferenceItem",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchReferenceItemAction(Request $request)
    {
        return $this->updateReferenceItem($request, false);
    }

    private function updateReferenceItem(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $referenceItem = $em->getRepository('AppBundle:ReferenceItem')->find($request->get('id'));
        /* @var $referenceItem ReferenceItem */
        if (empty($referenceItem)) {
            return new JsonResponse(['message' => 'ReferenceItem not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(ReferenceItemType::class, $referenceItem);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($referenceItem);
            $em->flush();
            return $referenceItem;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/reference-items/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="ReferenceItems",
     *     resource=true,
     *     description="Remove a referenceItem",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The referenceItem unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeReferenceItemAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $referenceItem = $em->getRepository('AppBundle:ReferenceItem')->find($request->get('id'));
        /* @var $referenceItem ReferenceItem */

        if ($referenceItem) {
            $em->remove($referenceItem);
            $em->flush();
        }
    }
}
