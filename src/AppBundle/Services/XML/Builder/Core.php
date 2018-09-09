<?php

namespace AppBundle\Services\XML\Builder;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Core
{
    private $em;
    private $header;
    private $content;
    private $functions;
    private $iiifServer;

    public function __construct(EntityManager $em, Header $header, Content $content, Functions $functions, $iiifServer)
    {
        $this->em = $em;
        $this->header = $header;
        $this->content = $content;
        $this->functions = $functions;
        $this->iiifServer = $iiifServer;
    }

    /**
     * @param $entity \AppBundle\Entity\Entity
     * @param $generate boolean
     * @return boolean|string|\DOMDocument
     */
    public function build($entity, $generate)
    {
        /* -- Definition of DOMDocument -- */
        /** @var $doc \DOMDocument */
        $doc = new \DOMDocument('1.0');
        $doc->encoding = 'UTF-8';
        $doc->formatOutput = true;

        /* -- Definition of the root element: TEI -- */
        $TEI = $doc->createElement('TEI');
        $TEI->setAttribute('xmlns', 'http://www.tei-c.org/ns/1.0');
        $TEI = $doc->appendChild($TEI);

        /* -- Building teiHeader -- */
        $teiHeader = $this->header->build($doc, $entity);
        $TEI->appendChild($teiHeader);

        /* -- Building facsimile -- */
        $facsimile = $doc->createElement('facsimile');
        $facsimile->setAttribute('xml:base', $this->iiifServer);
        $facsimile = $TEI->appendChild($facsimile);
        foreach($this->content->facsimile($doc, $entity) as $elem) {
            $facsimile->appendChild($elem);
        }

        /* -- Building text -- */
        $text = $doc->createElement('text');
        $text->setAttribute('xml:id', 'will_'.$entity->getWill()->getHostingOrganization()->getCode()."_".$this->functions->getIntIdToStrId($entity->getWillNumber(), 4));
        $text = $TEI->appendChild($text);

        $body = $doc->createElement('body');
        $body = $text->appendChild($body);

        /* -- Conversion of the content into XML -- */
        $content = $this->content->build($entity);
        $encodeContent = simplexml_load_string($content);
        $dom_content = dom_import_simplexml($encodeContent);
        if (!$dom_content) {
            echo 'Erreur lors de la conversion du XML';
            return false;
        }
        $dom_content = $doc->importNode($dom_content, true);
        $body->appendChild($dom_content);

        if($generate == true) {
            // TODO : Versionner l'export avec le datetime

            /* -- File generation -- */
            $filename = "testament_".$entity->getWill()->getHostingOrganization()->getCode()."_".$this->functions->getIntIdToStrId($entity->getWillNumber(), 4).".xml";
            $doc->save("download/".$filename);
            return $filename;
        } else {
            return $doc;
        }
    }

}