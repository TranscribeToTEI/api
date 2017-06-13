<?php

namespace TranscriptBundle\Controller;

use TranscriptBundle\Entity\Transcript;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Nelmio\ApiDocBundle\Annotation as Doc;

class TranscriptController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/transcripts/{id}",
     *     name = "transcript_transcript_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 200)
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Return one transcript",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the transcript.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function showAction(Transcript $transcript)
    {
        return $transcript;
    }

    /**
     * @Rest\Get(
     *    path = "/transcripts",
     *    name = "transcript_transcript_list"
     * )
     * @Rest\View(StatusCode = 200)
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
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository("TranscriptBundle:Transcript")->findAll();
    }

    /**
     * @Rest\Post(
     *    path = "/transcripts",
     *    name = "transcript_transcript_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("transcript", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Create a new transcript",
     *     requirements={
     *         {
     *             "name"="content",
     *             "dataType"="text",
     *             "requirement"="",
     *             "description"="The content of the transcript XML TEI."
     *         },
     *         {
     *             "name"="resource",
     *             "dataType"="entity",
     *             "requirement"="",
     *             "description"="The related resource of the transcript."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function createAction(Transcript $transcript)
    {
        //dump($transcript); die;
        $em = $this->getDoctrine()->getManager();
        $em->persist($transcript);
        $em->flush();

        return $this->view($transcript, Response::HTTP_CREATED, ['Location' => $this->generateUrl('transcript_transcript_show', ['id' => $transcript->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]);
    }

    /**
     * @Rest\Put(
     *    path = "/transcripts/{id}",
     *    name = "transcript_transcript_update",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200)
     * @ParamConverter("newTranscript", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Update an existing transcript",
     *     requirements={
     *         {
     *             "name"="content",
     *             "dataType"="text",
     *             "requirement"="",
     *             "description"="The content of the transcript XML TEI."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateAction(Transcript $transcript, Transcript $newTranscript)
    {
        $transcript->setContent($newTranscript->getContent());
        $this->getDoctrine()->getManager()->flush();

        return $transcript;
    }

    /**
     * @Rest\Delete(
     *     path = "/transcripts/{id}",
     *     name = "transcript_transcript_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 204)
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Remove a transcript",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the transcript.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function deleteAction(Transcript $transcript)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($transcript);
        $em->flush();

        return;
    }
}
