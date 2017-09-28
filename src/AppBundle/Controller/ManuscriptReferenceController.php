<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\ManuscriptReference;

use AppBundle\Form\ManuscriptReferenceType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ManuscriptReferenceController extends FOSRestController
{
    /**
     * @Rest\Get("/manuscript-references")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
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
    public function getManuscriptReferencesAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:ManuscriptReference');
        /* @var $repository EntityRepository */

        $manuscriptReferences = $repository->findAll();
        /* @var $manuscriptReferences ManuscriptReference[] */

        return $manuscriptReferences;
    }

    /**
     * @Rest\Get("/manuscript-references/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="ManuscriptReferences",
     *     resource=true,
     *     description="Return one manuscript reference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "manuscriptReferenceType"="integer",
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
    public function getManuscriptReferenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $manuscriptReference = $em->getRepository('AppBundle:ManuscriptReference')->find($request->get('id'));
        /* @var $manuscriptReference ManuscriptReference */

        if (empty($manuscriptReference)) {
            return new JsonResponse(['message' => 'ManuscriptReference not found'], Response::HTTP_NOT_FOUND);
        }

        return $manuscriptReference;
    }

    /**
     * @Rest\Post("/manuscriptReferences")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Manuscript-references",
     *     resource=true,
     *     description="Create a new manuscript reference",
     *     requirements={
     *
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_TAXONOMY_EDIT')")
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
     * @Rest\View()
     * @Rest\Put("/manuscript-references/{id}")
     * @Doc\ApiDoc(
     *     section="ManuscriptReferences",
     *     resource=true,
     *     description="Update an existing manuscript reference",
     *     requirements={
     *
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_TAXONOMY_EDIT')")
     */
    public function updateManuscriptReferenceAction(Request $request)
    {
        return $this->updateManuscriptReference($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/manuscript-references/{id}")
     * @Doc\ApiDoc(
     *     section="ManuscriptReferences",
     *     resource=true,
     *     description="Update an existing manuscript reference",
     *     requirements={
     *
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_TAXONOMY_EDIT')")
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
     *             "manuscriptReferenceType"="integer",
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
