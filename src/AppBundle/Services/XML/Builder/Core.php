<?php

namespace AppBundle\Services\XML\Builder;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Core
{
    private $em;
    private $header;
    private $content;
    private $functions;
    private $iiifServer;
    private $logger;

    public function __construct(EntityManager $em, Header $header, Content $content, Functions $functions, $iiifServer, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->header = $header;
        $this->content = $content;
        $this->functions = $functions;
        $this->iiifServer = $iiifServer;
        $this->logger = $logger;
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
        $teiHeader = $TEI->appendChild($teiHeader);

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
        /* -- Conversion of the content into XML -- */
        $content = "<body>".$this->content->build($entity)."</body>";

        $use_errors = libxml_use_internal_errors(true);
        $encodeContent = simplexml_load_string($content);
        if (false === $encodeContent) {
            $alert = $doc->createElement('div');
            $alert->setAttribute('type', 'alert');
            $alert->appendChild(new \DOMText("Attention, le code qui suit n'est pas valide."));
            $text->appendChild($alert);
            $text->appendChild(new \DOMText($content));
        } else {
            $dom_content = dom_import_simplexml($encodeContent);
            if (!$dom_content) {
                echo 'Erreur lors de la conversion du XML';
                return false;
            }
            $dom_content = $doc->importNode($dom_content, true);
            $text->appendChild($dom_content);
        }
        libxml_clear_errors();
        libxml_use_internal_errors($use_errors);

        if($generate == true) {
            /* -- File generation -- */
            $filename = "will_".$entity->getId()."_".$entity->getWill()->getHostingOrganization()->getCode()."_".$this->functions->getIntIdToStrId($entity->getWillNumber(), 4)."_".date('Y-m-d_h-i-s', time()).".xml";
            $doc->save("download/".$filename);
            return $filename;
        } else {
            return $doc;
        }
    }

}