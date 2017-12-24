<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\HostingOrganization;

use AppBundle\Form\HostingOrganizationType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;


use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class HostingOrganizationController extends FOSRestController
{
    /**
     * @Rest\Get("/hosting-organizations")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="search", nullable=true, description="Run a search query in the hosting organizations")
     *
     * @Doc\ApiDoc(
     *     section="HostingOrganizations",
     *     resource=true,
     *     description="Get the list of all hosting organizations",
     *     parameters={
     *         { "name"="search", "dataType"="string", "description"="Run a search query in the hosting organizations", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getHostingOrganizationsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $search = $paramFetcher->get('search');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:HostingOrganization');
        /* @var $repository EntityRepository */

        if($search != "") {
            $hostingOrganizations = $repository->findBy(array("name" => $search));
            /* @var $hostingOrganizations HostingOrganization[] */
        } else {
            $hostingOrganizations = $repository->findAll();
            /* @var $hostingOrganizations HostingOrganization[] */
        }

        return $hostingOrganizations;
    }

    /**
     * @Rest\Get("/hosting-organizations/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="HostingOrganizations",
     *     resource=true,
     *     description="Return one militaryUnit",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The hosting organization unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getHostingOrganizationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $militaryUnit = $em->getRepository('AppBundle:HostingOrganization')->find($request->get('id'));
        /* @var $militaryUnit HostingOrganization */

        if (empty($militaryUnit)) {
            return new JsonResponse(['message' => 'HostingOrganization not found'], Response::HTTP_NOT_FOUND);
        }

        return $militaryUnit;
    }

    /**
     * @Rest\Post("/hosting-organizations")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="HostingOrganizations",
     *     resource=true,
     *     description="Create a new hosting organization",
     *     input="AppBundle\Form\HostingOrganizationType",
     *     output="AppBundle\Entity\HostingOrganization",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function postHostingOrganizationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $militaryUnit = new HostingOrganization();
        $form = $this->createForm(HostingOrganizationType::class, $militaryUnit);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($militaryUnit);
            $em->flush();
            return $militaryUnit;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/hosting-organizations/{id}")
     * @Doc\ApiDoc(
     *     section="HostingOrganizations",
     *     resource=true,
     *     description="Update an existing hosting organization",
     *     input="AppBundle\Form\HostingOrganizationType",
     *     output="AppBundle\Entity\HostingOrganization",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updateHostingOrganizationAction(Request $request)
    {
        return $this->updateHostingOrganization($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/hosting-organizations/{id}")
     * @Doc\ApiDoc(
     *     section="HostingOrganizations",
     *     resource=true,
     *     description="Update an existing hosting organization",
     *     input="AppBundle\Form\HostingOrganizationType",
     *     output="AppBundle\Entity\HostingOrganization",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchHostingOrganizationAction(Request $request)
    {
        return $this->updateHostingOrganization($request, false);
    }

    private function updateHostingOrganization(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $militaryUnit = $em->getRepository('AppBundle:HostingOrganization')->find($request->get('id'));
        /* @var $militaryUnit HostingOrganization */
        if (empty($militaryUnit)) {
            return new JsonResponse(['message' => 'HostingOrganization not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(HostingOrganizationType::class, $militaryUnit);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($militaryUnit);
            $em->flush();
            return $militaryUnit;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/hosting-organizations/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="HostingOrganizations",
     *     resource=true,
     *     description="Remove a hosting organization",
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
    public function removeHostingOrganizationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $militaryUnit = $em->getRepository('AppBundle:HostingOrganization')->find($request->get('id'));
        /* @var $militaryUnit HostingOrganization */

        if ($militaryUnit) {
            $em->remove($militaryUnit);
            $em->flush();
        }
    }
}
