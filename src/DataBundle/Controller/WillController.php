<?php

namespace DataBundle\Controller;

use DataBundle\Entity\Will;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Nelmio\ApiDocBundle\Annotation as Doc;

class WillController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/wills/{id}",
     *     name = "data_will_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 200)
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
    public function showAction(Will $will)
    {
        return $will;
    }

    /**
     * @Rest\Get(
     *    path = "/wills",
     *    name = "data_will_list"
     * )
     * @Rest\View(StatusCode = 200)
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
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository("DataBundle:Will")->findAll();
    }

    /**
     * @Rest\Post(
     *    path = "/wills",
     *    name = "data_will_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("will", converter="fos_rest.request_body")
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
    public function createAction(Will $will)
    {
        //dump($will); die;
        $em = $this->getDoctrine()->getManager();
        $em->persist($will);
        $em->flush();

        return $this->view($will, Response::HTTP_CREATED, ['Location' => $this->generateUrl('data_will_show', ['id' => $will->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]);
    }

    /**
     * @Rest\Put(
     *    path = "/wills/{id}",
     *    name = "data_will_update",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200)
     * @ParamConverter("newWill", converter="fos_rest.request_body")
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
    public function updateAction(Will $will, Will $newWill)
    {
        $will->setTitle($newWill->getTitle());

        $this->getDoctrine()->getManager()->flush();

        return $will;
    }

    /**
     * @Rest\Delete(
     *     path = "/wills/{id}",
     *     name = "data_will_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 204)
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
    public function deleteAction(Will $will)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($will);
        $em->flush();

        return;
    }
}
