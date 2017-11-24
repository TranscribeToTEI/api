<?php

namespace AppBundle\Controller;

use AppBundle\Repository\TranscriptRepository;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Transcript;

use AppBundle\Form\TranscriptType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;

class DataInterfaceController extends FOSRestController
{
    /**
     * @Rest\Get("/interface/transcript/{idEntity}/{idResource}/{idTranscript}")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @Doc\ApiDoc(
     *     section="Transcripts",
     *     resource=true,
     *     description="Return a set of data required for the transcription",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getInterfaceFTranscriptAction(Request $request)
    {
        $arrayToReturn = array();

        $arrayToReturn['transcript'] = json_decode($this->forward('AppBundle:Transcript:getTranscript', array('id' => $request->get('idTranscript')))->getContent());
        $arrayToReturn['resource'] = json_decode($this->forward('AppBundle:Resource:getResource', array('id' => $request->get('idResource')))->getContent());
        $arrayToReturn['entity'] = json_decode($this->forward('AppBundle:Entity:getEntity', array('id' => $request->get('idEntity')))->getContent());

        return new JsonResponse($arrayToReturn);
    }
}
