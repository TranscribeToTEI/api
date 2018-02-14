<?php

namespace AppBundle\Services\XML;

use AppBundle\Services\XML\Builder\Core;
use Doctrine\ORM\EntityManager;

class Validator
{
    private $em;
    private $builder;
    private $relaxNGFile;

    public function __construct(EntityManager $em, Core $builder, $relaxNGFile)
    {
        $this->em = $em;
        $this->builder = $builder;
        $this->relaxNGFile = $relaxNGFile;
    }

    /**
     * @param $entity \AppBundle\Entity\Entity
     * @return array
     */
    public function validate($entity, $transcript)
    {
        /** @var $doc \DOMDocument */
        $doc = $this->builder->build($entity, false);
        $result = $doc->relaxNGValidate($this->relaxNGFile);
        #$doc = new \DOMDocument('1.0');
        #$doc->load('http://localhost:8888/TestamentsDePoilus/api/web/XMLModel/testWill.xml');

        return ["validation" => $result, "doc" => $doc->saveXML()];
        #return ["validation" => "ok", "doc" => $doc->saveXML()];

        #$xml_reader = new \XMLReader();
        #$xml_reader->xml('http://localhost:8888/TestamentsDePoilus/api/web/XMLModel/testWill.xml');
        #return ["validation" => $xml_reader->setRelaxNGSchema('http://localhost:8888/TestamentsDePoilus/api/web/XMLModel/TeiModel_for_FirstWorldWarWills.rng'), "doc" => ""];
    }
}