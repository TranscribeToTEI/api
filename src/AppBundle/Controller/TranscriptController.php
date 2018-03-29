<?php

namespace AppBundle\Controller;

use AppBundle\Repository\TranscriptRepository;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Transcript;

use AppBundle\Form\TranscriptType;
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

class TranscriptController extends FOSRestController
{
    /**
     * @Rest\Get("/transcripts")
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     * @QueryParam(name="status", nullable=true, description="Name of the status required")
     *
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Get the list of all transcripts",
     *     parameters={
     *         { "name"="status", "dataType"="string", "description"="Name of the status required", "required"=false },
     *     },
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
        /* @var $repository TranscriptRepository */
        if($status != "") {
            $transcripts = $repository->findBy(array("status" =>$status));
        } else {
            $transcripts = $repository->findAll();
        }
        /* @var $transcripts Transcript[] */

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "pageTranscript"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($transcripts, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/transcripts/{id}")
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Return one transcript",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
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
    public function getTranscriptAction(Request $request, ParamFetcher $paramFetcher)
    {
        //serializerGroups={"full"}
        $em = $this->getDoctrine()->getManager();
        $transcript = $em->getRepository('AppBundle:Transcript')->find($request->get('id'));
        /* @var $transcript Transcript */

        if (empty($transcript)) {
            return new JsonResponse(['message' => 'Transcript not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "pageTranscript", "metadata", "userProfile"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($transcript, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Post("/transcripts")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Create a new transcript",
     *     input="AppBundle\Form\TranscriptType",
     *     output="AppBundle\Entity\Transcript",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
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
     * @Rest\Put("/transcripts/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Update an existing transcript",
     *     input="AppBundle\Form\TranscriptType",
     *     output="AppBundle\Entity\Transcript",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updateTranscriptAction(Request $request, ParamFetcher $paramFetcher)
    {
        if($paramFetcher->get('profile') == '') {
            $profile = ["full"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return $this->updateTranscript($request, true, $profile);
    }

    /**
     * @Rest\Patch("/transcripts/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Update an existing transcript",
     *     input="AppBundle\Form\TranscriptType",
     *     output="AppBundle\Entity\Transcript",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchTranscriptAction(Request $request, ParamFetcher $paramFetcher)
    {
        if($paramFetcher->get('profile') == '') {
            $profile = ["full"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return $this->updateTranscript($request, false, $profile);
    }

    private function updateTranscript(Request $request, $clearMissing, $profile)
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

            if($form->get('sendNotification')->getData() == true and $transcript->getSubmitUser() != null and $this->get('user.user')->getPreference($transcript->getSubmitUser())->getNotificationTranscription() == true) {
                $entity = $this->get('app.transcript')->getResource($transcript)->getEntity();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Notification de traitement d\'une transcription - Testaments de Poilus')
                    ->setFrom('testaments-de-poilus@huma-num.fr')
                    ->setTo($transcript->getSubmitUser()->getEmail())
                    ->setBody($this->renderView(
                        'AppBundle:Transcript:notification.html.twig',
                        array('transcript' => $transcript, 'entity' => $entity)), 'text/html');
                $this->get('mailer')->send($message);
            }


            return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($transcript, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
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
     *     input="AppBundle\Form\TranscriptType",
     *     output="AppBundle\Entity\Transcript",
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
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
