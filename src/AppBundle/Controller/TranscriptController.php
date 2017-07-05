<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Transcript;

use AppBundle\Form\TranscriptType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;

class TranscriptController extends FOSRestController
{
    /**
     * @Rest\Get("/transcripts")
     * @Rest\View()
     *
     * @QueryParam(name="status", requirements="Status name", default="", description="Name of the status required")
     *
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Get the list of all transcripts",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTranscriptsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $status = $paramFetcher->get('status');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Transcript');
        /* @var $repository EntityRepository */
        if($status != "") {
            $transcripts = $repository->findBy(array("status" =>$status));
        } else {
            $transcripts = $repository->findAll();
        }
        /* @var $transcripts Transcript[] */

        return $transcripts;
    }

    /**
     * @Rest\Get("/transcripts/{id}")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Return one transcript",
     *     requirements={
     *         {
     *             "name"="id",
     *             "transcriptType"="integer",
     *             "requirement"="\d+",
     *             "description"="The transcript unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTranscriptAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $transcript = $em->getRepository('AppBundle:Transcript')->find($request->get('id'));
        /* @var $transcript Transcript */

        if (empty($transcript)) {
            return new JsonResponse(['message' => 'Transcript not found'], Response::HTTP_NOT_FOUND);
        }

        return $transcript;
    }

    /**
     * @Rest\Post("/transcripts")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Create a new transcript",
     *     requirements={
     *         {
     *             "name"="resource",
     *             "transcriptType"="integer",
     *             "requirement"="\d+",
     *             "description"="The identifier of the resource containing the transcript."
     *         },
     *         {
     *             "name"="content",
     *             "transcriptType"="text",
     *             "requirement"="\S+",
     *             "description"="The content of the transcript."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postTranscriptsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $transcript = new Transcript();
        $form = $this->createForm(TranscriptType::class, $transcript);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($transcript);
            $em->flush();
            return $transcript;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/transcripts/{id}")
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Update an existing transcript",
     *     requirements={
     *         {
     *             "name"="resource",
     *             "transcriptType"="integer",
     *             "requirement"="\d+",
     *             "description"="The identifier of the resource containing the transcript."
     *         },
     *         {
     *             "name"="content",
     *             "transcriptType"="text",
     *             "requirement"="\S+",
     *             "description"="The content of the transcript."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateTranscriptAction(Request $request)
    {
        return $this->updateTranscript($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/transcripts/{id}")
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Update an existing transcript",
     *     requirements={
     *         {
     *             "name"="resource",
     *             "transcriptType"="integer",
     *             "requirement"="\d+",
     *             "description"="The identifier of the resource containing the transcript."
     *         },
     *         {
     *             "name"="content",
     *             "transcriptType"="",
     *             "requirement"="",
     *             "description"="The content of the transcript."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchTranscriptAction(Request $request)
    {
        return $this->updateTranscript($request, false);
    }

    private function updateTranscript(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $transcript = $em->getRepository('AppBundle:Transcript')->find($request->get('id'));
        /* @var $transcript Transcript */
        if (empty($transcript)) {
            return new JsonResponse(['message' => 'Transcript not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(TranscriptType::class, $transcript);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($transcript);
            $em->flush();
            return $transcript;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/transcripts/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Remove a transcript",
     *     requirements={
     *         {
     *             "name"="id",
     *             "transcriptType"="integer",
     *             "requirement"="\d+",
     *             "description"="The transcript unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function removeTranscriptAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $transcript = $em->getRepository('AppBundle:Transcript')->find($request->get('id'));
        /* @var $transcript Transcript */

        if ($transcript) {
            $em->remove($transcript);
            $em->flush();
        }
    }
}
