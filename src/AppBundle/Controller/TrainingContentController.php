<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\TrainingContent;

use AppBundle\Form\TrainingContentType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TrainingContentController extends FOSRestController
{
    /**
     * @Rest\Get("/training-contents")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="status", nullable=true, description="Name of the status required")
     * @QueryParam(name="type", nullable=true, description="Name of the type required")
     * @QueryParam(name="order", nullable=true, description="Order index of the content required")
     * @QueryParam(name="orderInTraining", nullable=true, description="Are the results ordered by training order")
     *
     * @Doc\ApiDoc(
     *     section="TrainingContents",
     *     resource=true,
     *     description="Get the list of all training-contents",
     *     parameters={
     *         { "name"="status", "dataType"="string", "description"="Name of the status required", "required"=false },
     *         { "name"="type", "dataType"="string", "description"="Name of the type required", "required"=false },
     *         { "name"="order", "dataType"="string", "description"="Order index of the content required", "required"=false },
     *         { "name"="orderInTraining", "dataType"="string", "description"="Are the results ordered by training order", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTrainingContentsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $status = $paramFetcher->get('status');
        $type = $paramFetcher->get('type');
        $order = $paramFetcher->get('order');
        $orderInTraining = $paramFetcher->get('orderInTraining');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:TrainingContent');
        /* @var $repository EntityRepository */

        $query = [];
        if($status != "") {$query["status"] = $status;}
        if($type != "") {$query["type"] = $type;}
        if($order != "") {$query["orderInTraining"] = $order;}

        $order = [];
        if($orderInTraining == "ASC" or $orderInTraining == "DESC") {$order["orderInTraining"] = $orderInTraining;}

        $trainingContents = $repository->findBy($query, $order);
        /* @var $trainingContents TrainingContent[] */

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($trainingContents, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id', "trainingContent", "metadata", "userProfile"]))));
    }

    /**
     * @Rest\Get("/training-contents/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="TrainingContents",
     *     resource=true,
     *     description="Return one trainingContent",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The trainingContent unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTrainingContentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trainingContent = $em->getRepository('AppBundle:TrainingContent')->find($request->get('id'));
        /* @var $trainingContent TrainingContent */

        if (empty($trainingContent)) {
            return new JsonResponse(['message' => 'TrainingContent not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($trainingContent, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id', "trainingContent", "metadata", "userProfile"]))));
    }

    /**
     * @Rest\Post("/training-contents")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="TrainingContents",
     *     resource=true,
     *     description="Create a new trainingContent",
     *     input="AppBundle\Form\TrainingContentType",
     *     output="AppBundle\Entity\TrainingContent",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function postTrainingContentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $trainingContent = new TrainingContent();
        $form = $this->createForm(TrainingContentType::class, $trainingContent);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($trainingContent);
            $em->flush();
            return $trainingContent;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/training-contents/{id}")
     * @Doc\ApiDoc(
     *     section="TrainingContents",
     *     resource=true,
     *     description="Update an existing trainingContent",
     *     input="AppBundle\Form\TrainingContentType",
     *     output="AppBundle\Entity\TrainingContent",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function updateTrainingContentAction(Request $request)
    {
        return $this->updateTrainingContent($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/training-contents/{id}")
     * @Doc\ApiDoc(
     *     section="TrainingContents",
     *     resource=true,
     *     description="Update an existing trainingContent",
     *     input="AppBundle\Form\TrainingContentType",
     *     output="AppBundle\Entity\TrainingContent",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function patchTrainingContentAction(Request $request)
    {
        return $this->updateTrainingContent($request, false);
    }

    private function updateTrainingContent(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $trainingContent = $em->getRepository('AppBundle:TrainingContent')->find($request->get('id'));
        /* @var $trainingContent TrainingContent */
        if (empty($trainingContent)) {
            return new JsonResponse(['message' => 'TrainingContent not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(TrainingContentType::class, $trainingContent);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($trainingContent);
            $em->flush();
            return $trainingContent;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/training-contents/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="TrainingContents",
     *     resource=true,
     *     description="Remove a trainingContent",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The trainingContent unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeTrainingContentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trainingContent = $em->getRepository('AppBundle:TrainingContent')->find($request->get('id'));
        /* @var $trainingContent TrainingContent */

        if ($trainingContent) {
            $em->remove($trainingContent);
            $em->flush();
        }
    }
}
