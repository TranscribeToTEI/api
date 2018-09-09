<?php

namespace AppBundle\Services\XML\Builder;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Content
{
    private $em;
    private $functions;
    private $iiifServer;
    private $logger;

    public function __construct(EntityManager $em, Functions $functions, $iiifServer, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->functions = $functions;
        $this->iiifServer = $iiifServer;
        $this->logger = $logger;
    }

    /**
     * @param $entity \AppBundle\Entity\Entity
     * @return string
     */
    public function build($entity) {

        $count = 1;
        $prevTypeOfDiv = null;
        $text = "";

        foreach($entity->getResources() as $resource) {
            $typeOfDiv = "will";
            switch ($resource->getType()) {
                case "page":
                    $typeOfDiv = "will";
                    break;
                case "envelope":
                    $typeOfDiv = "envelope";
                    break;
                case "codicil":
                    $typeOfDiv = "codicil";
                    break;
            }


            if($count == 1) {
                $text .= '<div type="'.$typeOfDiv.'">';
            } elseif($count > 1 and $prevTypeOfDiv != null and $prevTypeOfDiv != $typeOfDiv) {
                $text .= "</div>";
                $text .= '<div type="'.$typeOfDiv.'">';
            }

            /** @var $resource \AppBundle\Entity\Resource */
            $text .= '<pb facs="#'.$resource->getImages()[0].'" />';

            if($resource->getTranscript()->getContent() != null) {
                $content = $resource->getTranscript()->getContent();
                if($resource->getTranscript()->getContinueBefore() == true) {
                    $content = $this->str_replace_first($content);
                }
                if($resource->getTranscript()->getContinueAfter() == true) {
                    $content = $this->str_lreplace($content);
                }
                $text .=  $content;
            }

            $prevTypeOfDiv = $typeOfDiv;
            $count++;
        }

        $text .= "</div>";
        return $text;
    }

    private function str_lreplace($content)
    {
        return substr($content, 0, strrpos($content, '</'));
    }

    private function str_replace_first($content)
    {
        return preg_replace('/<[^>]*>/', '', $content, 1);
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return array
     */
    public function facsimile($doc, $entity) {
        $listSurfaces = array();
        foreach ($entity->getResources() as $resource) {
            /** @var $resource \AppBundle\Entity\Resource */

            if(count($resource->getImages()) > 1) {
                $surfaceGrp = $doc->createElement('surfaceGrp');
                $surfaceGrp->setAttribute('type', $this->functions->getResourceTypeFormat($resource));
                $surfaceGrp->setAttribute('n', $this->functions->getIntIdToStrId($resource->getOrderInWill(), 2));

                foreach ($resource->getImages() as $image) {
                    $surface = $this->generateSurface($doc, $entity, $resource, $image);
                    $surface = $surfaceGrp->appendChild($surface);
                }
                $listSurfaces[] = $surfaceGrp;
            } else {
                $surface = $this->generateSurface($doc, $entity, $resource, $resource->getImages()[0]);
                $listSurfaces[] = $surface;
            }

        }

        return $listSurfaces;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @param $resource \AppBundle\Entity\Resource
     * @param $image string
     * @return \DOMElement
     */
    private function generateSurface($doc, $entity, $resource, $image) {
        $surface = $doc->createElement('surface');
        $surface->setAttribute('type', $this->functions->getResourceTypeFormat($resource));
        $surface->setAttribute('n', $this->functions->getIntIdToStrId($resource->getOrderInWill(), 2));

        $graphic = $doc->createElement('graphic');
        $graphic->setAttribute('url', "testament_".$entity->getWill()->getHostingOrganization()->getCode()."_".$this->functions->getIntIdToStrId($entity->getWillNumber(), 4)."___JPEG___".$image.".jpg");
        $graphic->setAttribute('xml:id', $resource->getImages()[0]);
        $surface->appendChild($graphic);

        return $surface;
    }
}