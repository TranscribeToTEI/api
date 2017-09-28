<?php

namespace AppBundle\Controller;

use AppBundle\Repository\PlaceNameRepository;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\PlaceName;

use AppBundle\Form\PlaceNameType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PlaceNameController extends FOSRestController
{
    /**
     * @Rest\Get("/place-names")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="PlaceNames",
     *     resource=true,
     *     description="Get the list of all place names",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getPlaceNamesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:PlaceName');
        /* @var $repository PlaceNameRepository */

       $placeNames = $repository->findAll();
        /* @var $placeNames PlaceName[] */

        return $placeNames;
    }

    /**
     * @Rest\Get("/place-names/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="PlaceNames",
     *     resource=true,
     *     description="Return one place name",
     *     requirements={
     *         {
     *             "name"="id",
     *             "placeNameType"="integer",
     *             "requirement"="\d+",
     *             "description"="The place name unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getPlaceNameAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $placeName = $em->getRepository('AppBundle:PlaceName')->find($request->get('id'));
        /* @var $placeName PlaceName */

        if (empty($placeName)) {
            return new JsonResponse(['message' => 'PlaceName not found'], Response::HTTP_NOT_FOUND);
        }

        return $placeName;
    }

    /**
     * @Rest\Post("/place-names")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="PlaceNames",
     *     resource=true,
     *     description="Create a new place name",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "placeNameType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the place name."
     *         },
     *         {
     *             "name"="PlaceName",
     *             "placeNameType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the place name."
     *         },
     *         {
     *             "name"="Type",
     *             "placeNameType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the place name."
     *         },
     *         {
     *             "name"="Status",
     *             "placeNameType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the place name."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function postPlaceNamesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $placeName = new PlaceName();
        $form = $this->createForm(PlaceNameType::class, $placeName);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($placeName);
            $em->flush();
            return $placeName;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/place-names/{id}")
     * @Doc\ApiDoc(
     *     section="PlaceNames",
     *     resource=true,
     *     description="Update an existing place name",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "placeNameType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the place name."
     *         },
     *         {
     *             "name"="PlaceName",
     *             "placeNameType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the place name."
     *         },
     *         {
     *             "name"="Type",
     *             "placeNameType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the place name."
     *         },
     *         {
     *             "name"="Status",
     *             "placeNameType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the place name."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function updatePlaceNameAction(Request $request)
    {
        return $this->updatePlaceName($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/place-names/{id}")
     * @Doc\ApiDoc(
     *     section="PlaceNames",
     *     resource=true,
     *     description="Update an existing place name",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "placeNameType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the place name."
     *         },
     *         {
     *             "name"="PlaceName",
     *             "placeNameType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the place name."
     *         },
     *         {
     *             "name"="Type",
     *             "placeNameType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the place name."
     *         },
     *         {
     *             "name"="Status",
     *             "placeNameType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the place name."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function patchPlaceNameAction(Request $request)
    {
        return $this->updatePlaceName($request, false);
    }

    private function updatePlaceName(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $placeName = $em->getRepository('AppBundle:PlaceName')->find($request->get('id'));
        /* @var $placeName PlaceName */
        if (empty($placeName)) {
            return new JsonResponse(['message' => 'PlaceName not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(PlaceNameType::class, $placeName);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($placeName);
            $em->flush();
            return $placeName;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/place-names/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_PlaceName)
     * @Doc\ApiDoc(
     *     section="PlaceNames",
     *     resource=true,
     *     description="Remove a place name",
     *     requirements={
     *         {
     *             "name"="id",
     *             "placeNameType"="integer",
     *             "requirement"="\d+",
     *             "description"="The place name unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removePlaceNameAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $placeName = $em->getRepository('AppBundle:PlaceName')->find($request->get('id'));
        /* @var $placeName PlaceName */

        if ($placeName) {
            $em->remove($placeName);
            $em->flush();
        }
    }
}
