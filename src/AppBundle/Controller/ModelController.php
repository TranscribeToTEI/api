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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;

class ModelController extends FOSRestController
{
    /**
     * @Rest\Get("/model")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @QueryParam(name="element", nullable=false, description="Name of the element required")
     * @QueryParam(name="elements", nullable=false, requirements="true", description="Query every elements info")
     * @QueryParam(name="info", nullable=false, requirements="full|doc|content|attributes|textAllowed", description="Type of information required")
     *
     * @Doc\ApiDoc(
     *     section="Model",
     *     resource=true,
     *     description="Get information about the validation model",
     *     parameters={
     *         { "name"="element", "dataType"="string", "description"="Name of the element required", "required"=false },
     *         { "name"="elements", "dataType"="string", "description"="Query every elements info", "required"=false },
     *         { "name"="info", "dataType"="string", "description"="Type of information required", "required"=false },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Cache(expires="1 January 2020", public=true)
     */
    public function getModelAction(Request $request, ParamFetcher $paramFetcher)
    {
        $element = $paramFetcher->get('element');
        if($element == "") {$element = null;}

        $elements = $paramFetcher->get('elements');
        if($elements == "true") {$elements = true;}

        if($element == null and $elements != true) {return new JsonResponse(['message' => 'Invalid element'], Response::HTTP_NOT_ACCEPTABLE);}

        $info = $paramFetcher->get('info');
        if(empty($info)) {return new JsonResponse(['message' => 'Invalid type of information'], Response::HTTP_NOT_ACCEPTABLE);}

        /** @var $modelService Model */
        $modelService = $this->get('app.xml.model');
        $data = null;

        if($element != null) {
            $data = $this->getInfo($element, $info);
        } elseif($elements == true) {
            $elementsList = $modelService->getAllElements();
            foreach ($elementsList as $element) {
                $data[$element] = $this->getInfo($element, $info);
            }
        }

        return new JsonResponse(["data" => $data], Response::HTTP_OK);
    }

    /**
     * @param $element string
     * @param $info string
     * @return array|string
     */
    private function getInfo($element, $info) {
        /** @var $modelService Model */
        $modelService = $this->get('app.xml.model');
        if ($info == 'doc') {
            return $modelService->getDoc($element);
        } elseif ($info == 'content') {
            return $modelService->getContent($element);
        } elseif ($info == 'attributes') {
            return $modelService->getAttributes($element);
        } elseif ($info == 'textAllowed') {
            return $modelService->getTextAllowed($element);
        } elseif ($info == 'full') {
            return [
                "doc" => $modelService->getDoc($element),
                "content" => $modelService->getContent($element),
                "attributes" => $modelService->getAttributes($element),
                "textAllowed" => $modelService->getTextAllowed($element)
            ];
        }
    }
}
