<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\AppPreference;

use AppBundle\Form\AppPreferenceType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AppPreferenceController extends FOSRestController
{
    /**
     * @Rest\Get("/app-preference")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="AppPreferences",
     *     resource=true,
     *     description="AppPreference allows to reach platform parameters. It should be only one value of config.",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getAppPreferencesAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:AppPreference');
        /* @var $repository EntityRepository */

        $appPreferences = $repository->findAll();
        /* @var $appPreferences AppPreference[] */

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($appPreferences, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id', 'content']))));
    }

    /**
     * @Rest\Get("/app-preference/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="AppPreferences",
     *     resource=true,
     *     description="Return one AppPreference entity",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The AppPreference unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getAppPreferenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $appPreference = $em->getRepository('AppBundle:AppPreference')->find($request->get('id'));
        /* @var $appPreference AppPreference */

        if (empty($appPreference)) {
            return new JsonResponse(['message' => 'AppPreference not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($appPreference, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id', 'content']))));
    }

    /**
     * @Rest\Post("/app-preference")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="AppPreferences",
     *     resource=true,
     *     description="Create a new AppPreference",
     *     input="AppBundle\Form\AppPreferenceType",
     *     output="AppBundle\Entity\AppPreference",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function postAppPreferencesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $appPreference = new AppPreference();
        $form = $this->createForm(AppPreferenceType::class, $appPreference);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($appPreference);
            $em->flush();
            return $appPreference;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/app-preference/{id}")
     * @Doc\ApiDoc(
     *     section="AppPreferences",
     *     resource=true,
     *     description="Update an existing appPreference",
     *     input="AppBundle\Form\AppPreferenceType",
     *     output="AppBundle\Entity\AppPreference",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function updateAppPreferenceAction(Request $request)
    {
        return $this->updateAppPreference($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/app-preference/{id}")
     * @Doc\ApiDoc(
     *     section="AppPreferences",
     *     resource=true,
     *     description="Update an existing appPreference",
     *     input="AppBundle\Form\AppPreferenceType",
     *     output="AppBundle\Entity\AppPreference",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function patchAppPreferenceAction(Request $request)
    {
        return $this->updateAppPreference($request, false);
    }

    private function updateAppPreference(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $appPreference = $em->getRepository('AppBundle:AppPreference')->find($request->get('id'));
        /* @var $appPreference AppPreference */
        if (empty($appPreference)) {
            return new JsonResponse(['message' => 'AppPreference not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(AppPreferenceType::class, $appPreference);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($appPreference);
            $em->flush();
            return $appPreference;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/app-preference/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="AppPreferences",
     *     resource=true,
     *     description="Remove a appPreference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The appPreference unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeAppPreferenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $appPreference = $em->getRepository('AppBundle:AppPreference')->find($request->get('id'));
        /* @var $appPreference AppPreference */

        if ($appPreference) {
            $em->remove($appPreference);
            $em->flush();
        }
    }
}
