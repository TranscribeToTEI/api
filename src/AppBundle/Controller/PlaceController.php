<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Place;

use AppBundle\Form\PlaceType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PlaceController extends FOSRestController
{
    /**
     * @Rest\Get("/places")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Get the list of all places",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getPlacesAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Place');
        /* @var $repository EntityRepository */

        $places = $repository->findAll();
        /* @var $places Place[] */

        return $places;
    }

    /**
     * @Rest\Get("/places/{id}")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Return one place",
     *     requirements={
     *         {
     *             "name"="id",
     *             "placeType"="integer",
     *             "requirement"="\d+",
     *             "description"="The place unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getPlaceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */

        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        return $place;
    }

    /**
     * @Rest\Post("/places")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Create a new place",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "placeType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the place."
     *         },
     *         {
     *             "name"="Place",
     *             "placeType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the place."
     *         },
     *         {
     *             "name"="Type",
     *             "placeType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the place."
     *         },
     *         {
     *             "name"="Status",
     *             "placeType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the place."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_THESAURUS_EDIT')")
     */
    public function postPlacesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($place);
            $em->flush();
            return $place;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/places/{id}")
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Update an existing place",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "placeType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the place."
     *         },
     *         {
     *             "name"="Place",
     *             "placeType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the place."
     *         },
     *         {
     *             "name"="Type",
     *             "placeType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the place."
     *         },
     *         {
     *             "name"="Status",
     *             "placeType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the place."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_THESAURUS_EDIT')")
     */
    public function updatePlaceAction(Request $request)
    {
        return $this->updatePlace($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/places/{id}")
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Update an existing place",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "placeType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the place."
     *         },
     *         {
     *             "name"="Place",
     *             "placeType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the place."
     *         },
     *         {
     *             "name"="Type",
     *             "placeType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the place."
     *         },
     *         {
     *             "name"="Status",
     *             "placeType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the place."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_THESAURUS_EDIT')")
     */
    public function patchPlaceAction(Request $request)
    {
        return $this->updatePlace($request, false);
    }

    private function updatePlace(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */
        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(PlaceType::class, $place);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($place);
            $em->flush();
            return $place;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/places/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Remove a place",
     *     requirements={
     *         {
     *             "name"="id",
     *             "placeType"="integer",
     *             "requirement"="\d+",
     *             "description"="The place unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removePlaceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */

        if ($place) {
            $em->remove($place);
            $em->flush();
        }
    }
}
