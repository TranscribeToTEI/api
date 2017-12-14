<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\commentLog;

use AppBundle\Form\CommentLogType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CommentLogController extends FOSRestController
{
    /**
     * @Rest\Get("/comment-logs")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="private",      nullable=true, description="Identifier of a specific comment")
     * @QueryParam(name="readByAdmin",  nullable=true, description="List of entities read or not by admin")
     * @QueryParam(name="count",        nullable=true, description="Do you want a number of results?")
     *
     * @Doc\ApiDoc(
     *     section="CommentLogs",
     *     resource=true,
     *     description="Get the list of all comment logs",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getCommentLogsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $private = $paramFetcher->get('private');
        $readByAdmin = $paramFetcher->get('readByAdmin');
        $count = $paramFetcher->get('count');
        if($readByAdmin != '') {
            $readByAdmin = $paramFetcher->get('readByAdmin');
        } else {$readByAdmin = null;}
        if($count != '') {
            $count = $paramFetcher->get('count');
        } else {$count = null;}

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:CommentLog');
        /* @var $repository EntityRepository */

        if($private != '') {
            return new JsonResponse(null);
        } elseif($readByAdmin != null) {
            $commentLogs = $repository->findBy(array('isReadByAdmin' => $readByAdmin));
            /* @var $commentLogs CommentLog[] */
        } else {
            $commentLogs = $repository->findAll();
            /* @var $commentLogs CommentLog[] */
        }

        if($count == true) {
            return new JsonResponse(count($commentLogs));
        } else {
            return $commentLogs;
        }
    }

    /**
     * @Rest\Get("/comment-logs/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="CommentLogs",
     *     resource=true,
     *     description="Return one printed reference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The comment log unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getCommentLogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commentLog = $em->getRepository('AppBundle:commentLog')->find($request->get('id'));
        /* @var $commentLog CommentLog */

        if (empty($commentLog)) {
            return new JsonResponse(['message' => 'commentLog not found'], Response::HTTP_NOT_FOUND);
        }

        return $commentLog;
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/comment-logs/{id}")
     * @Doc\ApiDoc(
     *     section="CommentLogs",
     *     resource=true,
     *     description="Update an existing comment log",
     *     input="AppBundle\Form\CommentLogType",
     *     output="AppBundle\Entity\CommentLog",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function updateCommentLogAction(Request $request)
    {
        return $this->updateCommentLog($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/comment-logs/{id}")
     * @Doc\ApiDoc(
     *     section="CommentLogs",
     *     resource=true,
     *     description="Update an existing comment log",
     *     input="AppBundle\Form\CommentLogType",
     *     output="AppBundle\Entity\CommentLog",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function patchCommentLogAction(Request $request)
    {
        return $this->updateCommentLog($request, false);
    }

    private function updateCommentLog(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $commentLog = $em->getRepository('AppBundle:CommentLog')->find($request->get('id'));
        /* @var $commentLog CommentLog */
        if (empty($commentLog)) {
            return new JsonResponse(['message' => 'CommentLog not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(CommentLogType::class, $commentLog);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($commentLog);
            $em->flush();
            return $commentLog;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/comment-logs/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="CommentLogs",
     *     resource=true,
     *     description="Remove a comment log",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The comment log unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeCommentLogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commentLog = $em->getRepository('AppBundle:CommentLog')->find($request->get('id'));
        /* @var $commentLog CommentLog */

        if ($commentLog) {
            $em->remove($commentLog);
            $em->flush();
        }
    }
}
