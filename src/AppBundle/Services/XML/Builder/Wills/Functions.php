<?php

namespace AppBundle\Services\XML\Builder\Wills;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Functions
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $name string
     * @return string
     */
    public function getIdFromName($name) {
        $id = '';
        $elements = explode(' ', $name);

        $surname = $elements[count($elements)-1];
        unset($elements[count($elements)-1]);

        foreach($elements as $element) {
            $detach = explode('-', $element);
            foreach($detach as $firstname) {
                $id .= $firstname[0];
            }
        }

        $id .= str_replace("-", "", $surname);

        return $id;
    }

    /**
     * @param $intId int
     * @param $length int
     * @return string
     */
    public function getIntIdToStrId($intId, $length) {
        $strId = "";
        $strIdConvert = strval($intId);

        if(strlen($strIdConvert) < $length) {
            for($i = strlen($strIdConvert); $i < $length; $i++) {
                $strId .= "0";
            }
            $strId .= $strIdConvert;
        } else {
            $strId = $strIdConvert;
        }

        return $strId;
    }

    /**
     * @param $resource \AppBundle\Entity\Resource
     * @return string
     */
    public function getResourceTypeFormat($resource) {
        $formatType = null;

        if(count($resource->getImages()) == 1 && $resource->getType() == "page") {
            $formatType = "will-page";
        } elseif(count($resource->getImages()) > 1 && $resource->getType() == "page") {
            $formatType = "will-page-part";
        } elseif($resource->getType() == "envelope") {
            /** @var Resource[] $resourcesEnvelopes */
            $resourcesEnvelopes = $this->em->getRepository('App:Resource')->findBy(array('type' => 'envelope', 'entity' => $resource->getEntity()));

            if(count($resourcesEnvelopes) > 1) {
                $arrayId = array();
                foreach($resourcesEnvelopes as $resourceI) {
                    $arrayId[] = $resourceI->getId();
                }
                sort($arrayId);

                if(array_search($resource->getId(), $arrayId) != false && array_search($resource->getId(), $arrayId) % 2 == 0) {
                    $formatType = "will-envelope-recto";
                } else {
                    $formatType = "will-envelope-verso";
                }
            } else {
                $formatType = "will-envelope-recto";
            }
        } elseif($resource->getType() == "codicil") {
            $formatType = "codicil-page";
        } else {
            $formatType = "unknown";
        }

        return $formatType;
    }

}