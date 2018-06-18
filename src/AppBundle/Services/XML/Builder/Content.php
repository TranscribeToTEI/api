<?php

namespace AppBundle\Services\XML\Builder;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Content
{
    private $em;
    private $functions;
    private $iiifServer;

    public function __construct(EntityManager $em, Functions $functions, $iiifServer)
    {
        $this->em = $em;
        $this->functions = $functions;
        $this->iiifServer = $iiifServer;
    }

    /**
     * @param $entity \AppBundle\Entity\Entity
     * @return string
     */
    public function build($entity) {

        foreach($entity->getResources() as $resource) {

            /* On lance la boucle :
                    1. On regarde le type de la première ressource et on ouvre un <div type="TYPE DE LA RESSOURCE">
                    2. On génère le <pb facs /> qui correspond à la ressource
                    3. On ajoute le texte de la transcription qui correspond à cette ressource
                    4. On vérifie qu'il ne faut pas enlever la première et la dernière balise
                    5. À la prochaine boucle, on vérifie que le type de la ressource est toujours le même et si ce n'est pas le cas, on le ferme et on en génère un nouveau.
             *
             * */


            $text = "<div type=\"will\">";
            /** @var $resource \AppBundle\Entity\Resource */
            $text .= "<pb facs=\"#testament-".$this->functions->getIntIdToStrId($entity->getWillNumber(), 4)."_vue-".$this->functions->getIntIdToStrId($resource->getOrderInWill(), 2)."_jpg\"/>"; // TODO : ici l'attribut facs correspond à XML-ID du facsimilé
            if($resource->getTranscript()->getContent() != null) {
                $text .= $resource->getTranscript()->getContent();
            }

            $text .= "</div>";
        }

        return $text;
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