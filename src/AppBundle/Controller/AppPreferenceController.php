<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\AppPreference;

use AppBundle\Form\AppPreferenceType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

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
     * @QueryParam(name="status", description="Name of the status required")
     * @QueryParam(name="type", description="")
     * @QueryParam(name="date", description="")
     * @QueryParam(name="limit", requirements="\d+", description="")
     *
     * @Doc\ApiDoc(
     *     section="AppPreferences",
     *     resource=true,
     *     description="Get the list of all app-preference",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getAppPreferencesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $status = $paramFetcher->get('status');
        $type = $paramFetcher->get('type');
        $date = $paramFetcher->get('date');
        $limit = $paramFetcher->get('limit');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:AppPreference');
        /* @var $repository EntityRepository */

        $query = [];
        if($status != "") {$query["status"] = $status;}
        if($type != "") {$query["type"] = $type;}

        $order = [];
        if($date == "ASC" or $date == "DESC") {$order["createDate"] = $date;}

        if($limit == "" or $limit == null) {$limit = 100;}

        $appPreferences = $repository->findBy($query, $order, $limit);
        /* @var $appPreferences AppPreference[] */

        return $appPreferences;
    }

    /**
     * @Rest\Get("/app-preference/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="AppPreferences",
     *     resource=true,
     *     description="Return one appPreference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "appPreferenceType"="integer",
     *             "requirement"="\d+",
     *             "description"="The appPreference unique identifier.",
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

        return $appPreference;
    }

    /**
     * @Rest\Post("/app-preference")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="AppPreferences",
     *     resource=true,
     *     description="Create a new appPreference",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "appPreferenceType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the appPreference."
     *         },
     *         {
     *             "name"="AppPreference",
     *             "appPreferenceType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the appPreference."
     *         },
     *         {
     *             "name"="Type",
     *             "appPreferenceType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the appPreference."
     *         },
     *         {
     *             "name"="Status",
     *             "appPreferenceType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the appPreference."
     *         }
     *     },
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
     *     requirements={
     *         {
     *             "name"="Title",
     *             "appPreferenceType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the appPreference."
     *         },
     *         {
     *             "name"="AppPreference",
     *             "appPreferenceType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the appPreference."
     *         },
     *         {
     *             "name"="Type",
     *             "appPreferenceType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the appPreference."
     *         },
     *         {
     *             "name"="Status",
     *             "appPreferenceType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the appPreference."
     *         }
     *     },
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
     *     requirements={
     *         {
     *             "name"="Title",
     *             "appPreferenceType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the appPreference."
     *         },
     *         {
     *             "name"="AppPreference",
     *             "appPreferenceType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the appPreference."
     *         },
     *         {
     *             "name"="Type",
     *             "appPreferenceType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the appPreference."
     *         },
     *         {
     *             "name"="Status",
     *             "appPreferenceType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the appPreference."
     *         }
     *     },
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
     *             "appPreferenceType"="integer",
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
