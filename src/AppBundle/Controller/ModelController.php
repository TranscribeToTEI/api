<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Transcript;
use AppBundle\Form\EntityType;
use AppBundle\Repository\EntityRepository;
use AppBundle\Repository\TranscriptRepository;
use AppBundle\Services\XML\Model;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;

class ModelController extends FOSRestController
{
    /**
     * @Rest\Get("/model")
     * @Rest\View()
     *
     * @QueryParam(name="element", nullable=false, description="Name of the element required")
     * @QueryParam(name="info", nullable=false, requirements="doc", description="Type of information required")
     *
     * @Doc\ApiDoc(
     *     section="Model",
     *     resource=true,
     *     description="Get information about the validation model",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function modelAction(Request $request, ParamFetcher $paramFetcher)
    {
        $element = $paramFetcher->get('element');
        if(empty($element)) {return new JsonResponse(['message' => 'Invalid element'], Response::HTTP_NOT_ACCEPTABLE);}
        $info = $paramFetcher->get('info');
        if(empty($info)) {return new JsonResponse(['message' => 'Invalid type of information'], Response::HTTP_NOT_ACCEPTABLE);}

        /** @var $modelService Model */
        $modelService = $this->get('app.xml.model');
        $doc = $modelService->getDoc($element);

        return new JsonResponse(['doc' => $doc], Response::HTTP_OK);
    }
}
