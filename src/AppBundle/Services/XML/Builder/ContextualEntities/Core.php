<?php

namespace AppBundle\Services\XML\Builder\ContextualEntities;

use AppBundle\Entity\MilitaryUnit;
use AppBundle\Entity\Place;
use AppBundle\Entity\Testator;
use AppBundle\Services\XML\Builder\Wills\Functions;
use AppBundle\Services\XML\Builder\ContextualEntities\Header;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class Core
{
    private $em;
    private $header;
    private $functions;
    private $iiifServer;
    private $logger;

    public function __construct(EntityManager $em, Header $header, Functions $functions, $iiifServer, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->header = $header;
        $this->functions = $functions;
        $this->iiifServer = $iiifServer;
        $this->logger = $logger;
    }

    /**
     * @param string $type
     * @param MilitaryUnit|Place|Testator $contextualEntities
     * @param $generate boolean
     * @return boolean|string|\DOMDocument
     */
    public function build($type, $contextualEntities, $generate)
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
        $teiHeader = $this->header->build($doc);
        $TEI->appendChild($teiHeader);

        /* -- Generation of the content -- */
        $text = $doc->createElement('text');
        $text = $TEI->appendChild($text);
        $body = $doc->createElement('body');
        $body = $text->appendChild($body);

        switch ($type) {
            case "militaryUnit":
                $containerTag = "listOrg";
                break;
            case "place":
                $containerTag = "listPlace";
                break;
            case "person":
                $containerTag = "listPerson";
                break;
            default:
                $containerTag = "undefinedList";
        }

        $listContainer = $doc->createElement($containerTag);
        $listContainer = $body->appendChild($listContainer);

        foreach ($contextualEntities as $contextualEntity) {
            switch ($type) {
                case "militaryUnit":
                    $tag = $this->buildMilitaryUnit($contextualEntity, $doc);
                    break;
                case "place":
                    $tag = $this->buildPlace($contextualEntity, $doc);
                    break;
                case "person":
                    $tag = $this->buildTestator($contextualEntity, $doc);
                    break;
                default:
                    $tag = new \DOMText("Erreur de chargement");
            }

            $tag = $listContainer->appendChild($tag);
        }

        if($generate == true) {
            /* -- File generation -- */
            $filename = "contextualEntity_".$type."_".date('Y-m-d_h-i-s', time()).".xml";
            $doc->save("download/".$filename);
            return $filename;
        } else {
            return $doc;
        }
    }

    /**
     * @param MilitaryUnit $militaryUnit
     * @param \DOMDocument $doc
     * @return \DOMElement
     */
    public function buildMilitaryUnit($militaryUnit, $doc) {
        $org = $doc->createElement('org');

        // Push here the content of the node


        return $org;
    }

    /**
     * @param Place $place
     * @param \DOMDocument $doc
     * @return \DOMElement
     */
    public function buildPlace($place, $doc) {
        $place = $doc->createElement('place');

        // Push here the content of the node


        return $place;
    }

    /**
     * @param Testator $testator
     * @param \DOMDocument $doc
     * @return \DOMElement
     */
    public function buildTestator($testator, $doc) {
        $person = $doc->createElement('person');

        // Push here the content of the node
        $persName = $doc->createElement('persName');
        $persName->setAttribute('type', 'fullProseForm');
        $persName = $person->appendChild($persName);

        $persNameText = new \DOMText($testator->getIndexName());
        $persName->appendChild($persNameText);

        return $person;
    }

}