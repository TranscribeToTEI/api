<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\ManuscriptReference;

use AppBundle\Form\ManuscriptReferenceType;
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

class ManuscriptReferenceController extends FOSRestController
{
    /**
     * @Rest\Get("/manuscript-references")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="ManuscriptReferences",
     *     resource=true,
     *     description="Get the list of all manuscript references",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getManuscriptReferencesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:ManuscriptReference');
        /* @var $repository EntityRepository */

        $manuscriptReferences = $repository->findAll();
        /* @var $manuscriptReferences ManuscriptReference[] */

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "bibliography", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($manuscriptReferences, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/manuscript-references/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="ManuscriptReferences",
     *     resource=true,
     *     description="Return one manuscript reference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The manuscript reference unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getManuscriptReferenceAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $manuscriptReference = $em->getRepository('AppBundle:ManuscriptReference')->find($request->get('id'));
        /* @var $manuscriptReference ManuscriptReference */

        if (empty($manuscriptReference)) {
            return new JsonResponse(['message' => 'ManuscriptReference not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "bibliography", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($manuscriptReference, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Post("/manuscript-references")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="ManuscriptReferences",
     *     resource=true,
     *     description="Create a new manuscript reference",
     *     input="AppBundle\Form\ManuscriptReferenceType",
     *     output="AppBundle\Entity\ManuscriptReference",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function postManuscriptReferencesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $manuscriptReference = new ManuscriptReference();
        $form = $this->createForm(ManuscriptReferenceType::class, $manuscriptReference);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($manuscriptReference);
            $em->flush();
            return $manuscriptReference;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/manuscript-references/{id}")
     * @Doc\ApiDoc(
     *     section="ManuscriptReferences",
     *     resource=true,
     *     description="Update an existing manuscript reference",
     *     input="AppBundle\Form\ManuscriptReferenceType",
     *     output="AppBundle\Entity\ManuscriptReference",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updateManuscriptReferenceAction(Request $request)
    {
        return $this->updateManuscriptReference($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/manuscript-references/{id}")
     * @Doc\ApiDoc(
     *     section="ManuscriptReferences",
     *     resource=true,
     *     description="Update an existing manuscript reference",
     *     input="AppBundle\Form\ManuscriptReferenceType",
     *     output="AppBundle\Entity\ManuscriptReference",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchManuscriptReferenceAction(Request $request)
    {
        return $this->updateManuscriptReference($request, false);
    }

    private function updateManuscriptReference(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $manuscriptReference = $em->getRepository('AppBundle:ManuscriptReference')->find($request->get('id'));
        /* @var $manuscriptReference ManuscriptReference */
        if (empty($manuscriptReference)) {
            return new JsonResponse(['message' => 'ManuscriptReference not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(ManuscriptReferenceType::class, $manuscriptReference);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($manuscriptReference);
            $em->flush();
            return $manuscriptReference;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/manuscript-references/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="ManuscriptReferences",
     *     resource=true,
     *     description="Remove a manuscript reference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The manuscript reference unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeManuscriptReferenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $manuscriptReference = $em->getRepository('AppBundle:ManuscriptReference')->find($request->get('id'));
        /* @var $manuscriptReference ManuscriptReference */

        if ($manuscriptReference) {
            $em->remove($manuscriptReference);
            $em->flush();
        }
    }
}
