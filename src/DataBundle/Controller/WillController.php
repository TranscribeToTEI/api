<?php

namespace DataBundle\Controller;

use DataBundle\Entity\Will;

use DataBundle\Form\WillType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nelmio\ApiDocBundle\Annotation as Doc;

class WillController extends FOSRestController
{
    /**
     * @Rest\Get("/wills")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of items per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Get the list of all wills",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getWillsAction(Request $request)
    {
        $wills = $this->getDoctrine()->getManager()->getRepository('DataBundle:Will')->findAll();
        /* @var $wills Will[] */

        return $wills;
    }

    /**
     * @Rest\Get("/wills/{id}")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Return one will",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The will unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getWillAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $will = $em->getRepository('DataBundle:Will')->find($request->get('id'));
        /* @var $will Will */

        if (empty($will)) {
            return new JsonResponse(['message' => 'Will not found'], Response::HTTP_NOT_FOUND);
        }

        return $will;
    }

    /**
     * @Rest\Post("/wills")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Create a new will",
     *     requirements={
     *         {
     *             "name"="title",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the will, can be a concatenation of the will number and the testator."
     *         },
     *         {
     *             "name"="number",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The number (cote) of the will."
     *         },
     *         {
     *             "name"="minute_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The date of the minute."
     *         },
     *         {
     *             "name"="will_writing_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The writing date of the will."
     *         },
     *         {
     *             "name"="will_writing_place",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The writing place of the will."
     *         },
     *         {
     *             "name"="testator",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The writing place of the will."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postWillsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $will = new Will();
        $form = $this->createForm(WillType::class, $will);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($will);
            $em->flush();
            return $will;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/wills/{id}")
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Update an existing will",
     *     requirements={
     *         {
     *             "name"="title",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the will, can be a concatenation of the will number and the testator."
     *         },
     *         {
     *             "name"="number",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The number (cote) of the will."
     *         },
     *         {
     *             "name"="minute_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The date of the minute."
     *         },
     *         {
     *             "name"="will_writing_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The writing date of the will."
     *         },
     *         {
     *             "name"="will_writing_place",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The writing place of the will."
     *         },
     *         {
     *             "name"="testator",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The writing place of the will."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateWillAction(Request $request)
    {
        return $this->updateWill($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/wills/{id}")
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Update an existing will",
     *     requirements={
     *         {
     *             "name"="title",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the will, can be a concatenation of the will number and the testator."
     *         },
     *         {
     *             "name"="number",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The number (cote) of the will."
     *         },
     *         {
     *             "name"="minute_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The date of the minute."
     *         },
     *         {
     *             "name"="will_writing_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The writing date of the will."
     *         },
     *         {
     *             "name"="will_writing_place",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The writing place of the will."
     *         },
     *         {
     *             "name"="testator",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The writing place of the will."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchWillAction(Request $request)
    {
        return $this->updateWill($request, false);
    }

    private function updateWill(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $will = $em->getRepository('DataBundle:Will')
            ->find($request->get('id'));
        /* @var $will Will */
        if (empty($will)) {
            return new JsonResponse(['message' => 'Will not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(WillType::class, $will);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->persist($will);
            $em->flush();
            return $will;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/wills/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Wills",
     *     resource=true,
     *     description="Remove a will",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The will unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function removeWillAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $will = $em->getRepository('DataBundle:Will')->find($request->get('id'));
        /* @var $will Will */

        if ($will) {
            $em->remove($will);
            $em->flush();
        }
    }
}
