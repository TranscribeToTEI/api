<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Transcript;
use AppBundle\Form\EntityType;
use AppBundle\Repository\EntityRepository;
use AppBundle\Repository\TranscriptRepository;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;

class ContributorController extends FOSRestController
{
    /**
     * @Rest\Get("/top-contributors")
     * @Rest\View()
     *
     * @QueryParam(name="count", nullable=false, requirements="\d+", description="Number of contributors")
     *
     * @Doc\ApiDoc(
     *     section="Contributors",
     *     resource=true,
     *     description="Return a list of the top contributors",
     *     parameters={
     *         { "name"="count", "dataType"="integer", "description"="Number of contributors", "required"=true },
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTopAction(Request $request, ParamFetcher $paramFetcher)
    {
        /** @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $count = $paramFetcher->get('count');

        $globalContributors = array();
        $resources = $em->getRepository('AppBundle:Resource')->findAll();
        foreach ($resources as $resource) {
            $contributorsOfResource = $this->get('app.resourcei')->getContributors($resource);

            foreach ($contributorsOfResource as $contributorId => $contributorOfResource) {
                if(array_key_exists($contributorId, $globalContributors) == true) {
                    $globalContributors[$contributorId]['contributions'] = $globalContributors[$contributorId]['contributions']+$contributorOfResource['contributions'];
                } elseif(array_key_exists($contributorId, $globalContributors) == false and (!in_array('ROLE_ADMIN', $contributorOfResource['user']->getRoles()) and !in_array('ROLE_MODO', $contributorOfResource['user']->getRoles()))) {
                    $globalContributors[$contributorId] = $contributorOfResource;
                }
            }
        }
        usort( $globalContributors, array($this, 'contributorSort'));
        array_splice($globalContributors, $count);

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($globalContributors, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups(["id", "userProfile"]))));
    }

    private function contributorSort( $a, $b ) {
        return $a['contributions'] == $b['contributions'] ? 0 : ( $a['contributions'] < $b['contributions'] ) ? 1 : -1;
    }
}
