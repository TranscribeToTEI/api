<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\TrainingResult;

use AppBundle\Form\TrainingResultType;
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

class TrainingResultController extends FOSRestController
{
    /**
     * @Rest\Get("/training-results")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="user", nullable=true, description="Id of the interrogated user")
     * @QueryParam(name="trainingContent", nullable=true, description="Id of the interrogated training content")
     *
     * @Doc\ApiDoc(
     *     section="TrainingResults",
     *     resource=true,
     *     description="Get the list of all training-results",
     *     parameters={
     *         { "name"="user", "dataType"="string", "description"="Id of the interrogated user", "required"=false },
     *         { "name"="trainingContent", "dataType"="string", "description"="Id of the interrogated training content", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTrainingResultsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $idUser = $paramFetcher->get('user');
        $idTrainingContent = $paramFetcher->get('trainingContent');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:TrainingResult');
        /* @var $repository EntityRepository */

        $query = [];
        if($idUser != "")               {$query["createUser"] = $idUser;}
        if($idTrainingContent != "")    {$query["trainingContent"] = $idTrainingContent;}

        $trainingResults = $repository->findBy($query, array('createDate' => 'DESC'));
        /* @var $trainingResults TrainingResult[] */

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($trainingResults, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id', "trainingResultContent", "metadata"]))));
    }

    /**
     * @Rest\Get("/training-results/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="TrainingResults",
     *     resource=true,
     *     description="Return one trainingResult",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The trainingResult unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTrainingResultAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trainingResult = $em->getRepository('AppBundle:TrainingResult')->find($request->get('id'));
        /* @var $trainingResult TrainingResult */

        if (empty($trainingResult)) {
            return new JsonResponse(['message' => 'TrainingResult not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($trainingResult, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(['id', "trainingResultContent", "trainingContent", "metadata", "userProfile"]))));
    }

    /**
     * @Rest\Post("/training-results")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="TrainingResults",
     *     resource=true,
     *     description="Create a new trainingResult",
     *     input="AppBundle\Form\TrainingResultType",
     *     output="AppBundle\Entity\TrainingResult",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function postTrainingResultsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $trainingResult = new TrainingResult();
        $form = $this->createForm(TrainingResultType::class, $trainingResult);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($trainingResult);
            $em->flush();
            return $trainingResult;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/training-results/{id}")
     * @Doc\ApiDoc(
     *     section="TrainingResults",
     *     resource=true,
     *     description="Update an existing trainingResult",
     *     input="AppBundle\Form\TrainingResultType",
     *     output="AppBundle\Entity\TrainingResult",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updateTrainingResultAction(Request $request)
    {
        return $this->updateTrainingResult($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/training-results/{id}")
     * @Doc\ApiDoc(
     *     section="TrainingResults",
     *     resource=true,
     *     description="Update an existing trainingResult",
     *     input="AppBundle\Form\TrainingResultType",
     *     output="AppBundle\Entity\TrainingResult",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchTrainingResultAction(Request $request)
    {
        return $this->updateTrainingResult($request, false);
    }

    private function updateTrainingResult(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $trainingResult = $em->getRepository('AppBundle:TrainingResult')->find($request->get('id'));
        /* @var $trainingResult TrainingResult */
        if (empty($trainingResult)) {
            return new JsonResponse(['message' => 'TrainingResult not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(TrainingResultType::class, $trainingResult);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($trainingResult);
            $em->flush();
            return $trainingResult;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/training-results/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="TrainingResults",
     *     resource=true,
     *     description="Remove a trainingResult",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The trainingResult unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeTrainingResultAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trainingResult = $em->getRepository('AppBundle:TrainingResult')->find($request->get('id'));
        /* @var $trainingResult TrainingResult */

        if ($trainingResult) {
            $em->remove($trainingResult);
            $em->flush();
        }
    }
}
