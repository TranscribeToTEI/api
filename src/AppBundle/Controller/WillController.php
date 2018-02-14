<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Will;

use AppBundle\Form\WillType;
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

class WillController extends FOSRestController
{
    /**
     * @Rest\Get("/wills")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Get the list of all wills",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getWillsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $wills = $this->getDoctrine()->getManager()->getRepository('AppBundle:Will')->findAll();
        /* @var $wills Will[] */

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "infoWill"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($wills, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/wills/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Return one will",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The will unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getWillAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $will = $em->getRepository('AppBundle:Will')->find($request->get('id'));
        /* @var $will Will */

        if (empty($will)) {
            return new JsonResponse(['message' => 'Will not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "infoWill", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($will, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Post("/wills")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Create a new will",
     *     input="AppBundle\Form\WillType",
     *     output="AppBundle\Entity\Will",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function postWillsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $will = new Will();
        $form = $this->createForm(WillType::class, $will);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($will);
            $em->flush();
            return $will;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/wills/{id}")
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Update an existing will",
     *     input="AppBundle\Form\WillType",
     *     output="AppBundle\Entity\Will",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function updateWillAction(Request $request)
    {
        return $this->updateWill($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/wills/{id}")
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Update an existing will",
     *     input="AppBundle\Form\WillType",
     *     output="AppBundle\Entity\Will",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function patchWillAction(Request $request)
    {
        return $this->updateWill($request, false);
    }

    private function updateWill(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $will = $em->getRepository('AppBundle:Will')
            ->find($request->get('id'));
        /* @var $will Will */
        if (empty($will)) {
            return new JsonResponse(['message' => 'Will not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(WillType::class, $will);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($will);
            $em->flush();
            return $will;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/wills/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Remove a will",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The will unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeWillAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $will = $em->getRepository('AppBundle:Will')->find($request->get('id'));
        /* @var $will Will */

        if ($will) {
            $em->remove($will);
            $em->flush();
        }
    }
}
