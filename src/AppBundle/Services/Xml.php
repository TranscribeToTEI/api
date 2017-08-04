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
     * @return boolean|string
     */
    public function build($entity, $transcript=null)
    {
        if($transcript != null) {
            $content = $transcript->getContent();
        } else {
            $content = $this->buildContent($entity);
        }

        /* -- Definition of DOMDocument -- */
        /** @var $doc \DOMDocument */
        $doc = new \DOMDocument('1.0');
        $doc->formatOutput = true;

        /* -- Definition of the root element: TEI -- */
        $TEI = $doc->createElement('TEI');
        $TEI->setAttribute('xmlns', 'http://www.tei-c.org/ns/1.0');
        $TEI = $doc->appendChild($TEI);

        /* -- Building TEI Header -- */
        $teiHeader = $this->buildTeiHeader($doc, $entity);
        $teiHeader = $TEI->appendChild($teiHeader);

        /* -- Building TEI Body -- */
        $text = $doc->createElement('text');
        $text->setAttribute('xml:id', 'will-'.$entity->getWill()->getId());
        $text = $TEI->appendChild($text);

        $front = $this->buildTextFront($doc, $entity);
        $front = $text->appendChild($front);

        $body = $doc->createElement('body');
        $body = $text->appendChild($body);

        /* -- Conversion of the content into XML -- */
        $encodeContent = simplexml_load_string($content);
        $dom_content = dom_import_simplexml($encodeContent);
        if (!$dom_content) {
            echo 'Erreur lors de la conversion du XML';
            return false;
        }
        $dom_content = $doc->importNode($dom_content, true);
        $body->appendChild($dom_content);

        /* -- File generation -- */
        $filename = $entity->getId().".xml";
        $doc->save("download/".$filename);
        return $filename;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function buildTeiHeader($doc, $entity)
    {
        /*
         * Generated structure:
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
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function buildTextFront($doc, $entity) {
        /*
         * Generated structure:
         *   <front>
         *      <docDate>
         *          <date when="1914-07-31">1914, 31 juillet</date>.
         *          <placeName>Paris</placeName>.
         *      </docDate>
         *      <div type="summary">
         *          <p>Testament olographe de <persName ref="#FLChatin">Fernand Lucien Jules Melchior <surname>Chatin</surname></persName>, mort pour la France à <placeName ref="#pl-056">Maroeuil (Pas-de-Calais)</placeName>, le 10 janvier 1915.</p>
         *      </div>
         *      <div type="physdesc">
         *          <p>Papier à lettre de deuil avec adresse imprimée, 4 pages, dim. 17,2 x 11,2 cm,
         *            encre.</p>
         *      </div>
         *      <div type="reference">
         *          <p><ref target="http://www.siv.archives-nationales.culture.gouv.fr/siv/rechercheconsultation/consultation/ir/consultationIR.action?irId=FRAN_IR_050355&amp;udId=c-9d519ean0--1d3br4vu5i0l3&amp;details=true&amp;auSeinIR=false#c-9d519ean0--1d3br4vu5i0l3" type="linkToANFWebsite">MC/ET/LIX/1082, minute du 29 avril 1915</ref></p>
         *      </div>
         *   </front>
         */

        $front = $doc->createElement('front');

        if($entity->getWill()->getWillWritingDate() != null && $entity->getWill()->getWillWritingPlace() != null) {
            $docDate = $doc->createElement('docDate');
            $docDate = $front->appendChild($docDate);

            if($entity->getWill()->getWillWritingDate() != null) {
                $date = $doc->createElement('date');
                $date->appendChild(new \DOMText($entity->getWill()->getWillWritingDate()->format('d-m-Y')));
                $date = $docDate->appendChild($date);
                $docDate->appendChild(new \DOMText("."));
            }

            if($entity->getWill()->getWillWritingPlace() != null) {
                $placeName = $doc->createElement('placeName');
                $placeName->appendChild(new \DOMText($entity->getWill()->getWillWritingPlace()->getName()));
                $placeName = $docDate->appendChild($placeName);
                $docDate->appendChild(new \DOMText("."));
            }
        }

        $divSummary = $this->buildFrontSummury($doc, $entity);
        $divSummary = $front->appendChild($divSummary);

        $divReference = $this->buildReference($doc, $entity);
        $divReference = $front->appendChild($divReference);

        return $front;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function buildFrontSummury($doc, $entity) {
        /*
         * <div type="summary">
         *      <p>
         *         Testament olographe de
         *         <persName ref="#FLChatin">Fernand Lucien Jules Melchior <surname>Chatin</surname></persName>,
         *         mort pour la France à <placeName ref="#pl-056">Maroeuil (Pas-de-Calais)</placeName>,
         *         le 10 janvier 1915.
         *      </p>
         * </div>
         *
         */

        $divSummary = $doc->createElement('div');
        $divSummary->setAttribute('type', 'summary');

        /* -- Building phrase -- */
        $text1 = new \DOMText("Testament olographe de ");
        $persName = $this->buildName($doc, $entity);
        $deathContextElements = $this->buildDeathContext($doc, $entity);

        /* -- Assembling elements -- */
        $pSummary = $doc->createElement('p');
        $pSummary->appendChild($text1);
        $pSummary->appendChild($persName);
        foreach($deathContextElements as $deathContext) {$pSummary->appendChild($deathContext);}
        $pSummary = $divSummary->appendChild($pSummary);

        return $divSummary;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function buildName($doc, $entity) {
        $arrayFornames = explode(' ', $entity->getWill()->getTestator()->getFirstnames());

        $persName = $doc->createElement('persName');
        $persName->setAttribute('ref', '#pers'.$entity->getWill()->getTestator()->getId());

        foreach($arrayFornames as $forname) {
            $fornameDoc = $doc->createElement('forname');
            $fornameDoc->appendChild(new \DOMText($forname));
            $fornameDoc = $persName->appendChild($fornameDoc);
        }

        $surname = $doc->createElement('surname');
        $surname->appendChild(new \DOMText($entity->getWill()->getTestator()->getSurname()));
        $surname = $persName->appendChild($surname);

        return $persName;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return array
     */
    private function buildDeathContext($doc, $entity) {
        /*
         * Generated structure:
         * mort pour la France à <placeName ref="#pl-056">Maroeuil (Pas-de-Calais)</placeName>, le 10 janvier 1915.
         */


        $elements = array();

        if($entity->getWill()->getTestator()->getDeathMention() != null) {
            $elements[] = new \DOMText(", ".$entity->getWill()->getTestator()->getDeathMention());
        } else {
            $elements[] = new \DOMText(", mort");
        }

        if($entity->getWill()->getTestator()->getPlaceOfDeath() != null) {
            $elements[] = new \DOMText(" à ");
            $placeName = $doc->createElement('placeName');
            $placeName->setAttribute('ref', '#pl-'.$entity->getWill()->getTestator()->getPlaceOfDeath()->getId());
            $placeName->appendChild(new \DOMText($entity->getWill()->getTestator()->getPlaceOfDeath()->getName()));
            $elements[] = $placeName;
        }
        if($entity->getWill()->getTestator()->getPlaceOfDeath() != null && $entity->getWill()->getTestator()->getDateOfDeath() != null) {
            $elements[] = new \DOMText(", ");
        }
        if($entity->getWill()->getTestator()->getDateOfDeath() != null) {
            $elements[] = new \DOMText(" le ".$entity->getWill()->getTestator()->getDateOfDeath()->format('d-m-Y'));
        }
        $elements[] = new \DOMText(".");

        return $elements;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function buildReference($doc, $entity) {
        /*
         * <div type="reference">
         *     <p><ref target="http://www.siv.archives-nationales.culture.gouv.fr/siv/rechercheconsultation/consultation/ir/consultationIR.action?irId=FRAN_IR_050355&amp;udId=c-9d519ean0--1d3br4vu5i0l3&amp;details=true&amp;auSeinIR=false#c-9d519ean0--1d3br4vu5i0l3" type="linkToANFWebsite">MC/ET/LIX/1082, minute du 29 avril 1915</ref></p>
         * </div>
         * MC/ET/LIX/1082, minute du 29 avril 1915
         */

        $divReference = $doc->createElement('div');
        $divReference->setAttribute('type', 'reference');

        $pReference = $doc->createElement('p');
        $pReference->appendChild(new \DOMText($entity->getWill()->getCallNumber().', minute du '.$entity->getWill()->getMinuteDate()->format('d-m-Y')));
        $pReference = $divReference->appendChild($pReference);

        return $divReference;
    }

    /**
     * @param $entity \AppBundle\Entity\Entity
     * @return string
     */
    private function buildContent($entity) {
        $text = "<div>";

        foreach($entity->getResources() as $resource) {
            /** @var $resource \AppBundle\Entity\Resource */
            if($resource->getTranscript()->getContent() != null) {
                $text .= $resource->getTranscript()->getContent();
            }
        }

        $text .= "</div>";
        return $text;
    }
}