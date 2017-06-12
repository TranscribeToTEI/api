<?php

namespace DataBundle\Controller;

use DataBundle\Entity\Will;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WillController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/wills/{id}",
     *     name = "data_will_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 201)
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
     * @Rest\View(StatusCode = 201)
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
     */
    public function createAction(Will $will)
    {
        //dump($will); die;
        $em = $this->getDoctrine()->getManager();
        $em->persist($will);
        $em->flush();

        return $this->view($will, Response::HTTP_CREATED, ['Location' => $this->generateUrl('data_will_show', ['id' => $will->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]);
    }
}
