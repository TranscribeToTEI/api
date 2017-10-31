<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\TranscriptLog;

use AppBundle\Form\TranscriptLogType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TranscriptLogController extends FOSRestController
{
    /**
     * @Rest\Get("/transcript-logs")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="transcript", nullable=true, description="Identifier of a specific transcript")
     * @QueryParam(name="isOpened", nullable=true, requirements="true|false", description="Return boolean if the transcript is opened")
     *
     * @Doc\ApiDoc(
     *     section="TranscriptLogs",
     *     resource=true,
     *     description="Get the list of all transcript logs",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTranscriptLogsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $transcript = $paramFetcher->get('transcript');
        $isOpened = boolval($paramFetcher->get('isOpened'));

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:TranscriptLog');
        /* @var $repository EntityRepository */

        if($transcript != '') {
            $array = ['transcript' => $transcript];
            if($isOpened != '') {
                $array['isOpened'] = $isOpened;
            }

            $transcriptLog = $repository->findOneBy($array);
            /* @var $transcriptLog TranscriptLog */
            if($transcriptLog != null) {
                return $transcriptLog;
            } else {
                return new JsonResponse(false);
            }
        } else {
            $transcriptLogs = $repository->findAll();
            /* @var $transcriptLogs TranscriptLog[] */
            return $transcriptLogs;
        }

    }

    /**
     * @Rest\Get("/transcript-logs/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="TranscriptLogs",
     *     resource=true,
     *     description="Return one printed reference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "transcriptLogType"="integer",
     *             "requirement"="\d+",
     *             "description"="The transcript log unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTranscriptLogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $transcriptLog = $em->getRepository('AppBundle:TranscriptLog')->find($request->get('id'));
        /* @var $transcriptLog TranscriptLog */

        if (empty($transcriptLog)) {
            return new JsonResponse(['message' => 'TranscriptLog not found'], Response::HTTP_NOT_FOUND);
        }

        return $transcriptLog;
    }

    /**
     * @Rest\Post("/transcript-logs")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="TranscriptLogs",
     *     resource=true,
     *     description="Create a new transcript log",
     *     requirements={
     *
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function postTranscriptLogsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $transcriptLog = new TranscriptLog();
        $form = $this->createForm(TranscriptLogType::class, $transcriptLog);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($transcriptLog);
            $em->flush();
            return $transcriptLog;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/transcript-logs/{id}")
     * @Doc\ApiDoc(
     *     section="TranscriptLogs",
     *     resource=true,
     *     description="Update an existing transcript log",
     *     requirements={
     *
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updateTranscriptLogAction(Request $request)
    {
        return $this->updateTranscriptLog($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/transcript-logs/{id}")
     * @Doc\ApiDoc(
     *     section="TranscriptLogs",
     *     resource=true,
     *     description="Update an existing transcript log",
     *     requirements={
     *
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchTranscriptLogAction(Request $request)
    {
        return $this->updateTranscriptLog($request, false);
    }

    private function updateTranscriptLog(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $transcriptLog = $em->getRepository('AppBundle:TranscriptLog')->find($request->get('id'));
        /* @var $transcriptLog TranscriptLog */
        if (empty($transcriptLog)) {
            return new JsonResponse(['message' => 'TranscriptLog not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(TranscriptLogType::class, $transcriptLog);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($transcriptLog);
            $em->flush();
            return $transcriptLog;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/transcript-logs/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="TranscriptLogs",
     *     resource=true,
     *     description="Remove a transcript log",
     *     requirements={
     *         {
     *             "name"="id",
     *             "transcriptLogType"="integer",
     *             "requirement"="\d+",
     *             "description"="The transcript log unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeTranscriptLogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $transcriptLog = $em->getRepository('AppBundle:TranscriptLog')->find($request->get('id'));
        /* @var $transcriptLog TranscriptLog */

        if ($transcriptLog) {
            $em->remove($transcriptLog);
            $em->flush();
        }
    }
}
