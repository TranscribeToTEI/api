<?php

namespace AppBundle\Controller;

use AppBundle\Repository\ContentRepository;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Content;

use AppBundle\Form\ContentType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ContentController extends FOSRestController
{
    /**
     * @Rest\Get("/contents")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="status", requirements="draft|public|private|notIndexed", nullable=true, description="Name of the status required")
     * @QueryParam(name="type", requirements="blogContent|helpContent|staticContent", nullable=true, description="Type of content")
     * @QueryParam(name="date", requirements="ASC|DESC", nullable=true, description="Sorting order")
     * @QueryParam(name="limit", requirements="\d*", nullable=true, description="Limit of results")
     * @QueryParam(name="onhomepage", requirements="true|false", nullable=true, description="homepage contents")
     *
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Get the list of all contents",
     *     parameters={
     *         { "name"="status", "dataType"="string", "description"="Name of the status required", "required"=false },
     *         { "name"="type", "dataType"="string", "description"="Type of content", "required"=false },
     *         { "name"="date", "dataType"="string", "description"="Sorting order", "required"=false },
     *         { "name"="limit", "dataType"="integer", "description"="Limit of results", "required"=false },
     *         { "name"="onhomepage", "dataType"="bool", "description"="homepage contents", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getContentsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $status = $paramFetcher->get('status');
        $type = $paramFetcher->get('type');
        $date = $paramFetcher->get('date');
        $limit = $paramFetcher->get('limit');
        $onHomepage = $paramFetcher->get('onhomepage');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Content');
        /* @var $repository ContentRepository */

        $query = [];
        if($status != "") {$query["status"] = $status;}
        if($type != "") {$query["type"] = $type;}
        if($onHomepage != "") {$query["onHomepage"] = $onHomepage;}

        $order = [];
        if($date == "ASC" or $date == "DESC") {$order["createDate"] = $date;}

        if($limit == "" or $limit == null) {$limit = 100;}

        $contents = $repository->findBy($query, $order, $limit);
        /* @var $contents Content[] */

        return $contents;
    }

    /**
     * @Rest\Get("/contents/{id}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Return one content",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The content unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getContentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Content')->find($request->get('id'));
        /* @var $content Content */

        if (empty($content)) {
            return new JsonResponse(['message' => 'Content not found'], Response::HTTP_NOT_FOUND);
        }

        return $content;
    }

    /**
     * @Rest\Post("/contents")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Create a new content",
     *     input="AppBundle\Form\ContentType",
     *     output="AppBundle\Entity\Content",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function postContentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($content);
            $em->flush();
            return $content;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Put("/contents/{id}")
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Update an existing content",
     *     input="AppBundle\Form\ContentType",
     *     output="AppBundle\Entity\Content",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function updateContentAction(Request $request)
    {
        return $this->updateContent($request, true);
    }

    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Patch("/contents/{id}")
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Update an existing content",
     *     input="AppBundle\Form\ContentType",
     *     output="AppBundle\Entity\Content",
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function patchContentAction(Request $request)
    {
        return $this->updateContent($request, false);
    }

    private function updateContent(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Content')->find($request->get('id'));
        /* @var $content Content */
        if (empty($content)) {
            return new JsonResponse(['message' => 'Content not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(ContentType::class, $content);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($content);
            $em->flush();
            return $content;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/contents/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Remove a content",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The content unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeContentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Content')->find($request->get('id'));
        /* @var $content Content */

        if ($content) {
            $em->remove($content);
            $em->flush();
        }
    }
}
