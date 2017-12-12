<?php

namespace CommentBundle\Controller;

use FOS\CommentBundle\Entity\Thread;
use FOS\CommentBundle\FOSCommentBundle;
use FOS\CommentBundle\Controller\ThreadController as BaseController;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;


use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ThreadController extends BaseController
{
    /**
     * Get the comments of a thread. Creates a new thread if none exists.
     *
     * @param Request $request Current request
     * @param string  $id      Id of the thread
     *
     * @return Response
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @todo Add support page/pagesize/sorting/tree-depth parameters
     */
    public function getThreadCommentsAction(Request $request, $id)
    {
        $displayDepth = $request->query->get('displayDepth');
        $sorter = $request->query->get('sorter');
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);

        // We're now sure it is no duplicate id, so create the thread
        if (null === $thread) {
            // Decode the permalink for cleaner storage (it is encoded on the client side)
            $permalink = urldecode($request->query->get('permalink'));

            $thread = $this->container->get('fos_comment.manager.thread')
                ->createThread();
            $thread->setId($id);
            $thread->setPermalink($permalink);

            // Validate the entity
            $validator = $this->get('validator');
            if ($validator instanceof ValidatorInterface) {
                $errors = $validator->validate($thread, null, array('NewThread'));
            } else {
                $errors = $validator->validate($thread, array('NewThread'));
            }
            if (count($errors) > 0) {
                $view = View::create()
                    ->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setData(array('errors' => $errors))
                    ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'errors'));

                return $this->getViewHandler()->handle($view);
            }

            // Add the thread
            $this->container->get('fos_comment.manager.thread')->saveThread($thread);
        }

        $viewMode = $request->query->get('view', 'tree');
        switch ($viewMode) {
            case self::VIEW_FLAT:
                $comments = $this->container->get('fos_comment.manager.comment')->findCommentsByThread($thread, $displayDepth, $sorter);

                // We need nodes for the api to return a consistent response, not an array of comments
                $comments = array_map(function ($comment) {
                    return array('comment' => $comment, 'children' => array());
                },
                    $comments
                );
                break;
            case self::VIEW_TREE:
            default:
                $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread, $sorter, $displayDepth);
                break;
        }

        $newComments = [];
        foreach ($comments as $comment) {
            $newComments[] = $this->get('jms_serializer')->serialize($comment, 'json', SerializationContext::create()->enableMaxDepthChecks());
        }

        $view = View::create()
            ->setData(array(
                'comments' => $newComments,
                'displayDepth' => $displayDepth,
                'sorter' => 'date',
                'thread' => $thread,
                'view' => $viewMode,
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comments'));

        // Register a special handler for RSS. Only available on this route.
        if ('rss' === $request->getRequestFormat()) {
            $templatingHandler = function ($handler, $view, $request) {
                $view->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'thread_xml_feed'));

                return new Response($handler->renderTemplate($view, 'rss'), Response::HTTP_OK, $view->getHeaders());
            };

            $this->get('fos_rest.view_handler')->registerHandler('rss', $templatingHandler);
        }

        return $this->getViewHandler()->handle($view);
    }

    /**
     * @return \FOS\RestBundle\View\ViewHandler
     */
    private function getViewHandler()
    {
        return $this->container->get('fos_rest.view_handler');
    }
}
