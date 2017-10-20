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
     * @return array
     */
    public function getDoc($element_name)
    {
        $doc = new \DOMDocument('1.0');
        $doc->load($this->model);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNameSpace('tei', 'http://www.tei-c.org/ns/1.0');
        $xpath->registerNameSpace('example', 'http://www.tei-c.org/ns/Examples');
        $documentation = [];

        $descriptions = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:desc');
        if($descriptions->length > 0) {
            /** @var $description \DOMNode */
            foreach($descriptions as $dKey => $description) {
                if($description->getAttribute('type') == "wills-ui") {
                    $documentation["projectDescription"] = $description->nodeValue;
                } else {
                    $documentation["descriptions"][] = ["date" => $description->getAttribute('versionDate'), "content" => $description->nodeValue];
                }
            }

        }

        $gloss = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:gloss');
        if($gloss->length > 0) {
            $gloss = $gloss[0];
            $documentation['gloss'][] = ["date" => $gloss->getAttribute('versionDate'), "content" => $gloss->nodeValue];
        }

        $exemplums = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:exemplum/example:egXML');
        if($exemplums->length > 0) {
            /** @var $exemplum \DOMNode */
            foreach($exemplums as $eKey => $exemplum) {
                $documentation["exemplum"][] = $doc->saveXML($exemplum);
            }
        }

        return $documentation;
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
        $elements = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:elementRef|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:alternate/tei:elementRef|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:alternate/tei:sequence/tei:elementRef|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:alternate/tei:alternate/tei:elementRef|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:sequence/tei:elementRef|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:sequence/tei:alternate/tei:elementRef');

        if($elements->length > 0) {
            /** @var $element \DOMNode */
            foreach ($elements as $element) {
                $arrayElem[] = $element->getAttribute('key');
            }
        }

        return $arrayElem;
    }

    /**
     * @param $element_name string
     * @return bool
     */
    public function getTextAllowed($element_name)
    {
        $textAllowed = false;
        $doc = new \DOMDocument('1.0');
        $doc->load($this->model);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNameSpace('tei', 'http://www.tei-c.org/ns/1.0');
        $elements = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:textNode|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:alternate/tei:textNode|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:alternate/tei:sequence/tei:textNode|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:alternate/tei:alternate/tei:textNode|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:sequence/tei:textNode|//tei:elementSpec[@ident="'.$element_name.'"]/tei:content/tei:sequence/tei:alternate/tei:textNode');

        if($elements->length > 0) {
            $textAllowed = true;
        }

        return $textAllowed;
    }

    /**
     * @param $element_name string
     * @return array
     */
    public function getAttributes($element_name)
    {
        $arrayAttr = [];
        $doc = new \DOMDocument('1.0');
        $doc->load($this->model);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNameSpace('tei', 'http://www.tei-c.org/ns/1.0');
        $attributes = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:attList/tei:attDef');

        if($attributes->length > 0) {
            /** @var $attribute \DOMNode */
            foreach ($attributes as $key => $attribute) {
                $valLists = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:attList/tei:attDef['.($key+1).']/tei:valList');
                $type = null;
                if($valLists->length > 0) {
                    $type = $valLists[0]->getAttribute('type');
                }

                $teiDesc = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:attList/tei:attDef['.($key+1).']/tei:desc');
                $desc = null;
                if($teiDesc->length > 0) {
                    $desc = $teiDesc[0]->nodeValue;
                }

                $teiValDesc = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:attList/tei:attDef['.($key+1).']/tei:valDesc');
                $valDesc = null;
                if($teiValDesc->length > 0) {
                    $valDesc = $teiValDesc[0]->nodeValue;
                }

                $valItems = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:attList/tei:attDef['.($key+1).']/tei:valList/tei:valItem');
                $valuesList = [];
                if($valItems->length > 0) {
                    foreach($valItems as $vIKey => $item) {
                        $itemGloss = $xpath->query('//tei:elementSpec[@ident="'.$element_name.'"]/tei:attList/tei:attDef['.($key+1).']/tei:valList/tei:valItem['.($vIKey+1).']/tei:gloss');
                        $gloss = null;
                        if($itemGloss->length > 0) {
                            $gloss = $itemGloss[0]->nodeValue;
                        }
                        $valuesList[] = ["value" => $item->getAttribute('ident'), "label" => $gloss];
                    }
                }

                $arrayAttr[$attribute->getAttribute('ident')] = [
                    "id" => $attribute->getAttribute('ident'),
                    "usage" => $attribute->getAttribute('usage'),
                    "type" => $type,
                    "desc" => $desc,
                    "valDesc" => $valDesc,
                    "values" => $valuesList
                ];
            }
        }

        return $arrayAttr;
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