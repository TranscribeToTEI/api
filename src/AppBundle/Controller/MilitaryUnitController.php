<?php

namespace AppBundle\Controller;

use AppBundle\Repository\MilitaryUnitRepository;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\MilitaryUnit;

use AppBundle\Form\MilitaryUnitType;
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

class MilitaryUnitController extends FOSRestController
{
    /**
     * @Rest\Get("/military-units")
     *
     * @QueryParam(name="search", nullable=true, description="Run a search query in the military units")
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="MilitaryUnits",
     *     resource=true,
     *     description="Get the list of all military units",
     *     parameters={
     *         { "name"="search", "dataType"="string", "description"="Run a search query in the military units", "required"=false },
     *         { "name"="profile",  "dataType"="string", "description"="Search profile to apply", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getMilitaryUnitsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $search = $paramFetcher->get('search');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:MilitaryUnit');
        /* @var $repository MilitaryUnitRepository */

        if($search != "") {
            $militaryUnits = $repository->findBy(array("name" => $search));
            /* @var $militaryUnits MilitaryUnit[] */
        } else {
            $militaryUnits = $repository->findAll();
            /* @var $militaryUnits MilitaryUnit[] */
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "taxonomyView"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($militaryUnits, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/military-units/{id}")
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="MilitaryUnits",
     *     resource=true,
     *     description="Return one militaryUnit",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The military unit unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getMilitaryUnitAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $militaryUnit = $em->getRepository('AppBundle:MilitaryUnit')->find($request->get('id'));
        /* @var $militaryUnit MilitaryUnit */

        if (empty($militaryUnit)) {
            return new JsonResponse(['message' => 'MilitaryUnit not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "taxonomyView", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($militaryUnit, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Post("/military-units")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="MilitaryUnits",
     *     resource=true,
     *     description="Create a new military unit",
     *     input="AppBundle\Form\MilitaryUnitType",
     *     output="AppBundle\Entity\MilitaryUnit",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function postMilitaryUnitsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $militaryUnit = new MilitaryUnit();
        $form = $this->createForm(MilitaryUnitType::class, $militaryUnit);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($militaryUnit);
            $em->flush();
            return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($militaryUnit, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id']))));
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/military-units/{id}")
     * @Doc\ApiDoc(
     *     section="MilitaryUnits",
     *     resource=true,
     *     description="Update an existing military unit",
     *     input="AppBundle\Form\MilitaryUnitType",
     *     output="AppBundle\Entity\MilitaryUnit",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updateMilitaryUnitAction(Request $request)
    {
        return $this->updateMilitaryUnit($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/military-units/{id}")
     * @Doc\ApiDoc(
     *     section="MilitaryUnits",
     *     resource=true,
     *     description="Update an existing military unit",
     *     input="AppBundle\Form\MilitaryUnitType",
     *     output="AppBundle\Entity\MilitaryUnit",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchMilitaryUnitAction(Request $request)
    {
        return $this->updateMilitaryUnit($request, false);
    }

    private function updateMilitaryUnit(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $militaryUnit = $em->getRepository('AppBundle:MilitaryUnit')->find($request->get('id'));
        /* @var $militaryUnit MilitaryUnit */
        if (empty($militaryUnit)) {
            return new JsonResponse(['message' => 'MilitaryUnit not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(MilitaryUnitType::class, $militaryUnit);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($militaryUnit);
            $em->flush();
            return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($militaryUnit, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id']))));
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/military-units/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="MilitaryUnits",
     *     resource=true,
     *     description="Remove a military unit",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The militaryUnit unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeMilitaryUnitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $militaryUnit = $em->getRepository('AppBundle:MilitaryUnit')->find($request->get('id'));
        /* @var $militaryUnit MilitaryUnit */

        if ($militaryUnit) {
            $em->remove($militaryUnit);
            $em->flush();
        }
    }
}
