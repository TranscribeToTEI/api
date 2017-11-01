<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Note;

use AppBundle\Form\NoteType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;


use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class NoteController extends FOSRestController
{
    /**
     * @Rest\Get("/notes")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="transcript", nullable=true, description="All notes from a transcript id")
     *
     * @Doc\ApiDoc(
     *     section="Notes",
     *     resource=true,
     *     description="Get the list of all notes",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getNotesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $transcript = $paramFetcher->get('transcript');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Note');
        /* @var $repository EntityRepository */

        if($transcript != "") {
            $notes = $repository->findBy(array('transcript' => $transcript));
            /* @var $notes Note[] */
        } else {
            $notes = $repository->findAll();
            /* @var $notes Note[] */
        }

        return $notes;
    }

    /**
     * @Rest\Get("/notes/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="Notes",
     *     resource=true,
     *     description="Return one note",
     *     requirements={
     *         {
     *             "name"="id",
     *             "noteType"="integer",
     *             "requirement"="\d+",
     *             "description"="The note unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getNoteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $note = $em->getRepository('AppBundle:Note')->find($request->get('id'));
        /* @var $note Note */

        if (empty($note)) {
            return new JsonResponse(['message' => 'Note not found'], Response::HTTP_NOT_FOUND);
        }

        return $note;
    }

    /**
     * @Rest\Post("/notes")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Notes",
     *     resource=true,
     *     description="Create a new note",
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
    public function postNotesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($note);
            $em->flush();
            return $note;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/notes/{id}")
     * @Doc\ApiDoc(
     *     section="Notes",
     *     resource=true,
     *     description="Update an existing note",
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
    public function updateNoteAction(Request $request)
    {
        return $this->updateNote($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/notes/{id}")
     * @Doc\ApiDoc(
     *     section="Notes",
     *     resource=true,
     *     description="Update an existing note",
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
    public function patchNoteAction(Request $request)
    {
        return $this->updateNote($request, false);
    }

    private function updateNote(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $note = $em->getRepository('AppBundle:Note')->find($request->get('id'));
        /* @var $note Note */
        if (empty($note)) {
            return new JsonResponse(['message' => 'Note not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(NoteType::class, $note);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($note);
            $em->flush();
            return $note;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/notes/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Notes",
     *     resource=true,
     *     description="Remove a note",
     *     requirements={
     *         {
     *             "name"="id",
     *             "noteType"="integer",
     *             "requirement"="\d+",
     *             "description"="The note unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeNoteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $note = $em->getRepository('AppBundle:Note')->find($request->get('id'));
        /* @var $note Note */

        if ($note) {
            $em->remove($note);
            $em->flush();
        }
    }
}
