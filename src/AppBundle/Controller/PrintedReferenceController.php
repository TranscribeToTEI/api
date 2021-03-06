<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\PrintedReference;

use AppBundle\Form\PrintedReferenceType;
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

class PrintedReferenceController extends FOSRestController
{
    /**
     * @Rest\Get("/printed-references")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="PrintedReferences",
     *     resource=true,
     *     description="Get the list of all printed references",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getPrintedReferencesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:PrintedReference');
        /* @var $repository EntityRepository */

        $printedReferences = $repository->findAll();
        /* @var $printedReferences PrintedReference[] */

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "bibliography", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($printedReferences, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/printed-references/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="PrintedReferences",
     *     resource=true,
     *     description="Return one printed reference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The printed reference unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getPrintedReferenceAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $printedReference = $em->getRepository('AppBundle:PrintedReference')->find($request->get('id'));
        /* @var $printedReference PrintedReference */

        if (empty($printedReference)) {
            return new JsonResponse(['message' => 'PrintedReference not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "bibliography", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($printedReference, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Post("/printed-references")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="PrintedReferences",
     *     resource=true,
     *     description="Create a new printed reference",
     *     input="AppBundle\Form\PrintedReferenceType",
     *     output="AppBundle\Entity\PrintedRefence",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function postPrintedReferencesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $printedReference = new PrintedReference();
        $form = $this->createForm(PrintedReferenceType::class, $printedReference);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($printedReference);
            $em->flush();
            return $printedReference;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/printed-references/{id}")
     * @Doc\ApiDoc(
     *     section="PrintedReferences",
     *     resource=true,
     *     description="Update an existing printed reference",
     *     input="AppBundle\Form\PrintedReferenceType",
     *     output="AppBundle\Entity\PrintedRefence",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updatePrintedReferenceAction(Request $request)
    {
        return $this->updatePrintedReference($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/printed-references/{id}")
     * @Doc\ApiDoc(
     *     section="PrintedReferences",
     *     resource=true,
     *     description="Update an existing printed reference",
     *     input="AppBundle\Form\PrintedReferenceType",
     *     output="AppBundle\Entity\PrintedRefence",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchPrintedReferenceAction(Request $request)
    {
        return $this->updatePrintedReference($request, false);
    }

    private function updatePrintedReference(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $printedReference = $em->getRepository('AppBundle:PrintedReference')->find($request->get('id'));
        /* @var $printedReference PrintedReference */
        if (empty($printedReference)) {
            return new JsonResponse(['message' => 'PrintedReference not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(PrintedReferenceType::class, $printedReference);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($printedReference);
            $em->flush();
            return $printedReference;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/printed-references/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="PrintedReferences",
     *     resource=true,
     *     description="Remove a printed reference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The printed reference unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removePrintedReferenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $printedReference = $em->getRepository('AppBundle:PrintedReference')->find($request->get('id'));
        /* @var $printedReference PrintedReference */

        if ($printedReference) {
            $referenceItem = $this->get('app.reference')->getReferenceItem('printedReference', $printedReference);
            $em->remove($referenceItem);
            $em->remove($printedReference);
            $em->flush();
        }
    }
}
