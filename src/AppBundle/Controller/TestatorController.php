<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Testator;

use AppBundle\Form\TestatorType;
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

class TestatorController extends FOSRestController
{
    /**
     * @Rest\Get("/testators")
     *
     * @QueryParam(name="search",   nullable=true, description="Run a search query in the testators")
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Get the list of all testators",
     *     parameters={
     *         { "name"="search",   "dataType"="string", "description"="Run a search query in the testators", "required"=false },
     *         { "name"="profile",  "dataType"="string", "description"="Search profile to apply", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTestatorsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $search = $paramFetcher->get('search');

        if($search != "") {
            $testators = $this->getDoctrine()->getManager()->getRepository('AppBundle:Testator')->findBy(array("name" => $search));
            /* @var $testators Testator[] */
        } else {
            $testators = $this->getDoctrine()->getManager()->getRepository('AppBundle:Testator')->findAll();
            /* @var $testators Testator[] */
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "content"];
        } else {
            $profile = $paramFetcher->get('profile');
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($testators, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/testators/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Return one testator",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the testator.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTestatorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $testator = $em->getRepository('AppBundle:Testator')->find($request->get('id'));
        /* @var $testator Testator */

        if (empty($testator)) {
            return new JsonResponse(['message' => 'Testator not found'], Response::HTTP_NOT_FOUND);
        }

        return $testator;
    }

    /**
     * @Rest\Post("/testators")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Create a new testator",
     *     input="AppBundle\Form\TestatorType",
     *     output="AppBundle\Entity\Testator",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function postTestatorsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $testator = new Testator();
        $form = $this->createForm(TestatorType::class, $testator);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($testator);
            $em->flush();
            return $testator;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/testators/{id}")
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Update an existing testator",
     *     input="AppBundle\Form\TestatorType",
     *     output="AppBundle\Entity\Testator",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updateTestatorAction(Request $request)
    {
        return $this->updateTestator($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/testators/{id}")
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Update an existing testator",
     *     input="AppBundle\Form\TestatorType",
     *     output="AppBundle\Entity\Testator",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchTestatorAction(Request $request)
    {
        return $this->updateTestator($request, false);
    }

    private function updateTestator(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $testator = $em->getRepository('AppBundle:Testator')
            ->find($request->get('id'));
        /* @var $testator Testator */
        if (empty($testator)) {
            return new JsonResponse(['message' => 'Testator not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(TestatorType::class, $testator);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($testator);
            $em->flush();
            return $testator;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/testators/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     *
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Remove a testator",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the testator.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeTestatorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $testator = $em->getRepository('AppBundle:Testator')->find($request->get('id'));
        /* @var $testator Testator */

        if ($testator) {
            $em->remove($testator);
            $em->flush();
        }
    }
}
