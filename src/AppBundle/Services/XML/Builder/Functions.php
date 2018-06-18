<?php

namespace AppBundle\Services\XML\Builder;

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
        } elseif(count($resource->getImages()) > 1 && $resource->getType() == "envelope") {
            // TODO : Ici, il faut faire attention si on a 2 enveloppes : il faut récupérer la liste de toutes les ressources
            // TODO : pour vérifier : 1: qu'il n'y a qu'une seule enveloppe, et 2: l'ordre des enveloppes pour pouvoir la qualifier
            // TODO : de recto ou de verso.
            $formatType = "will-envelope-recto";
        } else {
            $formatType = "unknown";
        }

        return $formatType;
    }

}