<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Xml
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $entity \AppBundle\Entity\Entity
     * @param $transcript \AppBundle\Entity\Transcript|null
     * @return boolean
     */
    public function build($entity, $transcript=null)
    {
        if($transcript != null) {
            $content = $transcript->getContent();
        } else {
            $content = $this->buildContent($entity);
        }

        /** @var $doc \DOMDocument */
        $doc = new \DOMDocument('1.0');
        $doc->formatOutput = true;

        // Création de la balise TEI
        $TEI = $doc->createElement('TEI');
        $TEI->setAttribute('xmlns', 'http://www.tei-c.org/ns/1.0');
        $TEI = $doc->appendChild($TEI);

        $teiHeader = $this->getTeiHeader($doc, $entity);
        $TEI->appendChild($teiHeader);

        $text = $doc->createElement('text');
        $TEI->appendChild($text);

        $body = $doc->createElement('body');
        $body = $text->appendChild($body);

        $encodeContent = simplexml_load_string($content);
        $dom_content = dom_import_simplexml($encodeContent);
        if (!$dom_content) {
            echo 'Erreur lors de la conversion du XML';
            exit;
        }
        $dom_content = $doc->importNode($dom_content, true);
        $body->appendChild($dom_content);

        $save = $entity->getId().".xml";
        $doc->save("download/".$save);
        return $save;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function getTeiHeader($doc, $entity)
    {
        /*
         * Structure generated:
         *   <teiHeader>
         *       <fileDesc>
         *           <titleStmt>
         *               <title>A sample TEI document</title>
         *           </titleStmt>
         *           <publicationStmt>
         *               <publisher> KANTL </publisher>
         *               <pubPlace>Ghent</pubPlace>
         *               <date when="2009"/>
         *           </publicationStmt>
         *           <sourceDesc>
         *               <p>No source, born digital</p>
         *           </sourceDesc>
         *       </fileDesc>
         *   </teiHeader>
         *
         * ******* WARNING:
         * The teiHeader contains information about the export, not about the will
         * */


        $teiHeader = $doc->createElement('teiHeader');

        $fileDesc = $doc->createElement('fileDesc');
        $fileDesc = $teiHeader->appendChild($fileDesc);

        $titleStmt = $doc->createElement('titleStmt');
        $titleStmt = $fileDesc->appendChild($titleStmt);

        $title = $doc->createElement('title');
        $title->appendChild(new \DOMText("Entity ".$entity->getId().", containing the will of ".$entity->getWill()->getTestator()->getName()));
        $titleStmt->appendChild($title);

        $publicationStmt = $doc->createElement('publicationStmt');
        $publicationStmt = $fileDesc->appendChild($publicationStmt);

        $publisher = $doc->createElement('publisher');
        $publisher->appendChild(new \DOMText("TranscribeToTEI software - project Testaments de Poilus"));
        $publicationStmt->appendChild($publisher);

        $pubPlace = $doc->createElement('pubPlace');
        $pubPlace->appendChild(new \DOMText("Paris"));
        $publicationStmt->appendChild($pubPlace);

        $date = $doc->createElement('date');
        $date->setAttribute("when", date("Y-m-d H:i:s"));
        $publicationStmt->appendChild($date);

        $sourceDesc = $doc->createElement('sourceDesc');
        $sourceDesc = $fileDesc->appendChild($sourceDesc);

        $p = $doc->createElement('p');
        $p->appendChild(new \DOMText("https://testaments-de-poilus.huma-num.fr"));
        $sourceDesc->appendChild($p);

        return $teiHeader;
    }

    /**
     * @param $entity \AppBundle\Entity\Entity
     * @return string
     */
    private function buildContent($entity) {
        $text = "";

        foreach($entity->getResources() as $resource) {
            /** @var $resource \AppBundle\Entity\Resource */
            if($resource->getTranscript()->getContent() != null) {
                $text .= $resource->getTranscript()->getContent();
            }
        }

        return $text;
    }
}