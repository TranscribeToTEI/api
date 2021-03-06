<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Place;

use AppBundle\Form\PlaceType;
use FOS\RestBundle\Context\Context;
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

class PlaceController extends FOSRestController
{
    /**
     * @Rest\Get("/places")
     *
     * @QueryParam(name="search", nullable=true, description="Run a search query in the places")
     * @QueryParam(name="profile", nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Get the list of all places",
     *     parameters={
     *         { "name"="search", "dataType"="string", "description"="Run a search query in the places", "required"=false },
     *         { "name"="profile", "dataType"="string", "description"="Search profile to apply", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getPlacesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $search = $paramFetcher->get('search');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Place');
        /* @var $repository EntityRepository */

        if($search != "") {
            /* @var $places Place[] */
            $qb = $repository->createQueryBuilder('a')
                        ->where('a.name = :name')
                        ->setParameter('name', $search);
            $places = $qb->getQuery()->getResult();
        } else {
            $places = $repository->findAll();
            /* @var $places Place[] */
        }


        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "taxonomyView"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($places, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/places/{id}")
     * @QueryParam(name="profile", nullable=true, description="Search profile to apply")
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Return one place",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
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
    public function getPlaceAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */

        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "taxonomyView", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($place, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Post("/places")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Create a new place",
     *     input="AppBundle\Form\PlaceType",
     *     output="AppBundle\Entity\Place",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
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
            return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($place, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id']))));
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/places/{id}")
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Update an existing place",
     *     input="AppBundle\Form\PlaceType",
     *     output="AppBundle\Entity\Place",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updatePlaceAction(Request $request)
    {
        return $this->updatePlace($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/places/{id}")
     * @Doc\ApiDoc(
     *     section="Places",
     *     resource=true,
     *     description="Update an existing place",
     *     input="AppBundle\Form\PlaceType",
     *     output="AppBundle\Entity\Place",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
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
            return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($place, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id']))));
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
     *             "dataType"="integer",
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
