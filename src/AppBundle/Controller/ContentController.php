<?php

namespace AppBundle\Controller;

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

class ContentController extends FOSRestController
{
    /**
     * @Rest\Get("/contents")
     * @Rest\View()
     *
     * @QueryParam(name="status", requirements="Status name", default="", description="Name of the status required")
     *
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Get the list of all contents",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getContentsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $status = $paramFetcher->get('status');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Content');
        /* @var $repository EntityRepository */
        if($status != "") {
            $contents = $repository->findBy(array("status" =>$status));
        } else {
            $contents = $repository->findAll();
        }
        /* @var $contents Content[] */

        return $contents;
    }

    /**
     * @Rest\Get("/contents/{id}")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Return one content",
     *     requirements={
     *         {
     *             "name"="id",
     *             "contentType"="integer",
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
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Create a new content",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "contentType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the content."
     *         },
     *         {
     *             "name"="Content",
     *             "contentType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the content."
     *         },
     *         {
     *             "name"="Type",
     *             "contentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the content."
     *         },
     *         {
     *             "name"="Status",
     *             "contentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the content."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
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
     * @Rest\View()
     * @Rest\Put("/contents/{id}")
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Update an existing content",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "contentType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the content."
     *         },
     *         {
     *             "name"="Content",
     *             "contentType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the content."
     *         },
     *         {
     *             "name"="Type",
     *             "contentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the content."
     *         },
     *         {
     *             "name"="Status",
     *             "contentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the content."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateContentAction(Request $request)
    {
        return $this->updateContent($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/contents/{id}")
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Update an existing content",
     *     requirements={
     *         {
     *             "name"="Title",
     *             "contentType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the content."
     *         },
     *         {
     *             "name"="Content",
     *             "contentType"="text",
     *             "requirement"="\S+",
     *             "description"="The text of the content."
     *         },
     *         {
     *             "name"="Type",
     *             "contentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The type of the content."
     *         },
     *         {
     *             "name"="Status",
     *             "contentType"="text",
     *             "requirement"="\S{0,255}",
     *             "description"="The status of the content."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
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
     *             "contentType"="integer",
     *             "requirement"="\d+",
     *             "description"="The content unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
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
