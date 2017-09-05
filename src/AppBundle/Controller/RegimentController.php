<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Regiment;

use AppBundle\Form\RegimentType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RegimentController extends FOSRestController
{
    /**
     * @Rest\Get("/regiments")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Regiments",
     *     resource=true,
     *     description="Get the list of all regiments",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getRegimentsAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Regiment');
        /* @var $repository EntityRepository */

        $regiments = $repository->findAll();
        /* @var $regiments Regiment[] */

        return $regiments;
    }

    /**
     * @Rest\Get("/regiments/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="Regiments",
     *     resource=true,
     *     description="Return one regiment",
     *     requirements={
     *         {
     *             "name"="id",
     *             "regimentType"="integer",
     *             "requirement"="\d+",
     *             "description"="The regiment unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getRegimentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $regiment = $em->getRepository('AppBundle:Regiment')->find($request->get('id'));
        /* @var $regiment Regiment */

        if (empty($regiment)) {
            return new JsonResponse(['message' => 'Regiment not found'], Response::HTTP_NOT_FOUND);
        }

        return $regiment;
    }

    /**
     * @Rest\Post("/regiments")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Regiments",
     *     resource=true,
     *     description="Create a new regiment",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "regimentType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the regiment."
     *         },
     *         {
     *             "name"="Regiment",
     *             "regimentType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the regiment."
     *         },
     *         {
     *             "name"="Type",
     *             "regimentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the regiment."
     *         },
     *         {
     *             "name"="Status",
     *             "regimentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the regiment."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_TAXONOMY_EDIT')")
     */
    public function postRegimentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $regiment = new Regiment();
        $form = $this->createForm(RegimentType::class, $regiment);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($regiment);
            $em->flush();
            return $regiment;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/regiments/{id}")
     * @Doc\ApiDoc(
     *     section="Regiments",
     *     resource=true,
     *     description="Update an existing regiment",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "regimentType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the regiment."
     *         },
     *         {
     *             "name"="Regiment",
     *             "regimentType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the regiment."
     *         },
     *         {
     *             "name"="Type",
     *             "regimentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the regiment."
     *         },
     *         {
     *             "name"="Status",
     *             "regimentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the regiment."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_TAXONOMY_EDIT')")
     */
    public function updateRegimentAction(Request $request)
    {
        return $this->updateRegiment($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/regiments/{id}")
     * @Doc\ApiDoc(
     *     section="Regiments",
     *     resource=true,
     *     description="Update an existing regiment",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "regimentType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the regiment."
     *         },
     *         {
     *             "name"="Regiment",
     *             "regimentType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the regiment."
     *         },
     *         {
     *             "name"="Type",
     *             "regimentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the regiment."
     *         },
     *         {
     *             "name"="Status",
     *             "regimentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the regiment."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_TAXONOMY_EDIT')")
     */
    public function patchRegimentAction(Request $request)
    {
        return $this->updateRegiment($request, false);
    }

    private function updateRegiment(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $regiment = $em->getRepository('AppBundle:Regiment')->find($request->get('id'));
        /* @var $regiment Regiment */
        if (empty($regiment)) {
            return new JsonResponse(['message' => 'Regiment not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(RegimentType::class, $regiment);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($regiment);
            $em->flush();
            return $regiment;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/regiments/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Regiments",
     *     resource=true,
     *     description="Remove a regiment",
     *     requirements={
     *         {
     *             "name"="id",
     *             "regimentType"="integer",
     *             "requirement"="\d+",
     *             "description"="The regiment unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeRegimentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $regiment = $em->getRepository('AppBundle:Regiment')->find($request->get('id'));
        /* @var $regiment Regiment */

        if ($regiment) {
            $em->remove($regiment);
            $em->flush();
        }
    }
}
