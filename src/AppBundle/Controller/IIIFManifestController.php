<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppPreference;
use AppBundle\Repository\ContentRepository;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Content;

use AppBundle\Form\ContentType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use IIIF;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class IIIFManifestController extends FOSRestController
{
    /**
     * @Rest\Get("/manifests")
     * @Rest\View(statusCode=Response::HTTP_OK)
     *
     * @Doc\ApiDoc(
     *     section="IIIF",
     *     resource=true,
     *     description="Get the list of the manifests",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     *
     * @param $request Request
     * @return JsonResponse
     */
    public function getManifestsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        return new JsonResponse(true);
    }

    /**
     * @Rest\Get("/manifests/new")
     * @Rest\View(statusCode=Response::HTTP_OK)
     *
     * @Doc\ApiDoc(
     *     section="IIIF",
     *     resource=true,
     *     description="Generate a new manifest",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     *
     * @param $request Request
     * @return JsonResponse
     */
    public function getNewManifestAction(Request $request)
    {
        $manifest = new IIIF\PresentationAPI\Resources\Manifest(true);

        $manifest->setID("http://example.org/iiif/book1/manifest");
        $manifest->addLabel("Book 1");

        $sequence = new IIIF\PresentationAPI\Resources\Sequence();
        $manifest->addSequence($sequence);
        $sequence->setID("http://example.org/iiif/book1/sequence/normal");
        $sequence->addLabel("Current Page Order");

        $canvas = new IIIF\PresentationAPI\Resources\Canvas();
        $sequence->addCanvas($canvas);
        $canvas->setID("http://example.org/iiif/book1/canvas/p1");
        $canvas->addLabel("p. 1");
        $canvas->setWidth(500);
        $canvas->setHeight(500);

        return new JsonResponse($manifest);
    }
}
