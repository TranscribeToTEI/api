<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppPreference;
use AppBundle\Repository\ContentRepository;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Content;

use AppBundle\Form\ContentType;
use FOS\CommentBundle\Entity\Thread;
use FOS\CommentBundle\Entity\ThreadManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ThreadController extends FOSRestController
{
    /**
     * @Rest\Get("/override-threads")
     * @Rest\View(statusCode=Response::HTTP_OK, serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="user", description="Id of user requested")
     *
     * @Doc\ApiDoc(
     *     section="Threads",
     *     resource=true,
     *     description="List of threads",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     *
     * @param $request Request
     * @return Thread[]
     */
    public function getThreadsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $paramFetcher->get('user');

        /** @var $threads Thread[] */
        $threads = $this->container->get('fos_comment.manager.thread')->findAllThreads();

        if($user != "") {
            $selectedThreads = array();
            foreach ($threads as $thread) {
                if(strpos($thread->getId(), "user-") !== false and strpos($thread->getId(), $user) !== false) {
                    $selectedThreads[] = $thread;
                }
            }
            $threads = $selectedThreads;
        }

        return $threads;
    }
}
