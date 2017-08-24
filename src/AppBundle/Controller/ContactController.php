<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppPreference;
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

class ContactController extends FOSRestController
{
    /**
     * @Rest\Post("/contact")
     * @Rest\View(statusCode=Response::HTTP_OK)
     *
     * @Doc\ApiDoc(
     *     section="Contents",
     *     resource=true,
     *     description="Create a new content",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     *
     * @param $request Request
     * @return JsonResponse
     */
    public function postContactsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $preference AppPreference */
        $preference = $em->getRepository('AppBundle:AppPreference')->findAll()[0];

        $this->get('app.contact')->sendEmail($preference->getContactEmail(), $request->request->get('email'), $request->request->get('name'), $request->request->get('object'), $request->request->get('message'), $preference->getProjectTitle());

        return new JsonResponse(true);
    }
}
