<?php

namespace AppBundle\Services\XML\Builder\ContextualEntities;

use AppBundle\Services\XML\Builder\Wills\Functions;
use AppBundle\Services\XML\Builder\Wills\Header;
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
     * @param $contextualEntity \AppBundle\Entity\Entity
     * @param $generate boolean
     * @return boolean|string|\DOMDocument
     */
    public function build($contextualEntity, $generate)
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
        $teiHeader = $this->header->build($doc, $contextualEntity);
        $TEI->appendChild($teiHeader);



        if($generate == true) {
            /* -- File generation -- */
            $filename = "contextualEntity_".$contextualEntity->getId()."_".date('Y-m-d_h-i-s', time()).".xml";
            $doc->save("download/".$filename);
            return $filename;
        } else {
            return $doc;
        }
    }

}