<?php

namespace AppBundle\Controller;

use AppBundle\Repository\CommentLogRepository;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\commentLog;

use AppBundle\Form\CommentLogType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use JMS\Serializer\SerializationContext;
use Psr\Log\LoggerInterface;
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
     * @QueryParam(name="private",      nullable=true, description="Identifier of a specific user")
     * @QueryParam(name="readByAdmin",  nullable=true, description="List of entities read or not by admin")
     * @QueryParam(name="count",        nullable=true, description="Do you want a number of results?")
     * @QueryParam(name="profile",      nullable=true, description="Search profile to apply")
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
        $toReturn = null;
        $private = $paramFetcher->get('private');
        $readByAdmin = $paramFetcher->get('readByAdmin');
        $count = $paramFetcher->get('count');

        if($readByAdmin == '')  {$readByAdmin = null;}
        if($count == '') {$count = null;}
        if($private == '') {$private = null;}

        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "commentLog", "name"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:CommentLog');
        /* @var $repository CommentLogRepository */

        if($private != null) {
            if(count(explode('-', $private)) > 1) {
                $listUsers = explode('-', $private);
            } else {
                $listUsers = [$private];
            }

            if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or ($this->get('security.authorization_checker')->isGranted('ROLE_USER') and in_array($this->getUser()->getId(), $listUsers))) {
                $threadType = "users";
                $commentLogs = $repository->getCommentsByThreadId($threadType, $listUsers);
                /* @var $commentLogs CommentLog[] */
                $toReturn = $commentLogs;
            } else {
                return new JsonResponse(['message' => 'not allowed']);
            }
        } elseif($readByAdmin != null) {
            $commentLogs = $repository->findBy(array('isReadByAdmin' => $readByAdmin), array('createDate' => "DESC"));
            /* @var $commentLogs CommentLog[] */
        } else {
            $commentLogs = $repository->findBy(array('isPrivateThread' => false), array('createDate' => "DESC"));
            /* @var $commentLogs CommentLog[] */
        }

        if($count == true) {
            $toReturn = count($commentLogs);
        } else if($private == null) {
            $toReturn = $commentLogs;
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($toReturn, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
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
        $commentLog = $em->getRepository('AppBundle:CommentLog')->find($request->get('id'));
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
