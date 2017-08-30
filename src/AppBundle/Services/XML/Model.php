<?php

namespace AppBundle\Services\XML;

use Doctrine\ORM\EntityManager;

class Model
{
    private $em;
    private $model;

    public function __construct(EntityManager $em, $model)
    {
        $this->em = $em;
        $this->model = $model;
    }

    /**
     * @param $element_name string
     * @return string
     */
    public function getDoc($element_name)
    {
        $foundElem = null;
        $doc = new \DOMDocument('1.0');
        $doc->load($this->model);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNameSpace('tei', 'http://www.tei-c.org/ns/1.0');
        $elements = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:desc[@type="wills-ui"]');

        if($elements->length > 0) {
            /** @var $element \DOMNode */
            $element = $elements[0];
            $foundElem = $element->nodeValue;
        } else {
            $elements = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:desc');
            if($elements->length > 0) {
                /** @var $element \DOMNode */
                $element = $elements[0];
                $foundElem = $element->nodeValue;
            }
        }

        return $foundElem;
    }

    /**
     * @param $element_name string
     * @return array
     */
    public function getContent($element_name)
    {
        $arrayElem = [];
        $doc = new \DOMDocument('1.0');
        $doc->load($this->model);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNameSpace('tei', 'http://www.tei-c.org/ns/1.0');
        $elements = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:elementRef|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:alternate/tei:elementRef|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:alternate/tei:sequence/tei:elementRef|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:sequence/tei:elementRef|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:sequence/tei:alternate/tei:elementRef');

        if($elements->length > 0) {
            /** @var $element \DOMNode */
            foreach ($elements as $element) {
                $arrayElem[] = $element->getAttribute('key');
            }
        }

        return $arrayElem;
    }

    /**
     * @return array
     */
    public function getAllElements()
    {
        $arrayElems = [];
        $doc = new \DOMDocument('1.0');
        $doc->load($this->model);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNameSpace('tei', 'http://www.tei-c.org/ns/1.0');
        $elements = $xpath->query('//tei:elementSpec');

        if($elements->length > 0) {
            /** @var $element \DOMNode */
            foreach ($elements as $element) {
                $arrayElems[] = $element->getAttribute('ident');
            }
        }

        return $arrayElems;
    }
}