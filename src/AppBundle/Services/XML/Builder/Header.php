<?php

namespace AppBundle\Services\XML\Builder;

use Doctrine\ORM\EntityManager;

class Header
{
    private $em;
    private $functions;

    public function __construct(EntityManager $em, Functions $functions)
    {
        $this->em = $em;
        $this->functions = $functions;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    public function build($doc, $entity)
    {
        /*
         * Generated structure:
         *   <teiHeader>
         *    <!-- FC pour l'en-tête TEI, le modèle reste à faire. Tous les éléments TEI sont aujourd'hui disponibles -->
         *         <fileDesc>
         *            <titleStmt>
         *               <title>Title</title>
         *               <!-- mentions de responsabilité : le fichier TEI final doit stocker la liste des contributeurs : transcription, annotation, validation -->
         *            </titleStmt>
         *            <publicationStmt>
         *               <p>Publication Information</p>
         *            </publicationStmt>
         *            <sourceDesc></sourceDesc>
         *         </fileDesc>
         *         <revisionDesc></revisionDesc>
         *      </teiHeader>
         * */


        $teiHeader = $doc->createElement('teiHeader');

        $fileDesc = $doc->createElement('fileDesc');
        $fileDesc = $teiHeader->appendChild($fileDesc);

        /* -- Title Statement -- */
        $titleStmt = $doc->createElement('titleStmt');

        $title = $doc->createElement('title');
        $title->appendChild(new \DOMText($entity->getWill()->getTitle()));
        $titleStmt->appendChild($title);

        $titleStmt = $this->buildRespStmts($doc, $entity, $titleStmt);

        $fileDesc->appendChild($titleStmt);
        /* -- End : Title Statement -- */

        /* -- Publication Statement -- */
        $publicationStmt = $doc->createElement('publicationStmt');
        $publicationStmt = $fileDesc->appendChild($publicationStmt);

        $publisher = $doc->createElement('publisher');
        $publisher->appendChild(new \DOMText("Project Testaments de Poilus"));
        $publicationStmt->appendChild($publisher);

        $pubPlace = $doc->createElement('pubPlace');
        $pubPlace->appendChild(new \DOMText("Paris"));
        $publicationStmt->appendChild($pubPlace);

        $date = $doc->createElement('date');
        $date->setAttribute("when", date("Y-m-d"));
        $date->setAttribute("type", "otherDate");
        $publicationStmt->appendChild($date);
        /* -- End : Publication Statement -- */

        /* -- Source Description -- */
        $fileDesc->appendChild($this->buildSourceDesc($doc, $entity));
        /* -- END = Source Description -- */

        /* -- Revision Description -- */
        $teiHeader->appendChild($this->buildRevisionDesc($doc, $entity));
        /* -- END = Revision Description -- */

        return $teiHeader;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @param $titleStmt \DOMNode
     * @return \DOMNode
     */
    private function buildRespStmts($doc, $entity, $titleStmt) {
        /*
         * <respStmt>
         *   <resp>identification du testament</resp>
         *   <persName xml:id="tata">Serge Machin</persName>
         * </respStmt>
         * <!-- <respStmt>
         *    <!-\- généré autoamtiquement -\->
         *    <resp>numérisation</resp>
         *    <orgName>labo photo des AN</orgName>
         * </respStmt>-->
         * <respStmt>
         *    <resp>transcription</resp>
         *    <persName xml:id="toto"
         *       ><!--<surname>Dupont</surname><forename>Lily</forename>-->toto</persName>
         *    <persName xml:id="MDurand"
         *       ><surname>Durand</surname><forename>Michel</forename></persName>
         * </respStmt>
         * <respStmt>
         *    <resp>annotation</resp>
         *    <persName corresp="#MDurand">
         *       <surname>Durand</surname><forename>Michel</forename>
         *    </persName>
         * </respStmt>
         * <respStmt>
         *    <resp>validation</resp>
         *    <persName xml:id="FClavaud"
         *       ><surname>Clavaud</surname><forename>Florence</forename></persName>
         * </respStmt>
         */


        $respStmtIdentifier = $this->buildRespStmt($doc, $entity, "identification");
        $titleStmt->appendChild($respStmtIdentifier);

        return $titleStmt;
    }



    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @param $context string
     * @return \DOMNode
     */
    private function buildRespStmt($doc, $entity, $context) {
        /*
         * <respStmt>
         *   <resp>identification du testament</resp>
         *   <persName xml:id="tata">Serge Machin</persName>
         * </respStmt>
         */

        $statement = $doc->createElement('respStmt');
        $resp = $doc->createElement('resp');
        $persName = $doc->createElement('persName');

        switch ($context) {
            case "identification":
                $respText = new \DOMText("Identification du testament");
                $persNameText = new \DOMText($entity->getWill()->getIdentificationUsers());
                $persNameId = $this->functions->getIdFromName($entity->getWill()->getIdentificationUsers());
                break;
            default:
                $respText = new \DOMText('Error');
                $persNameText = new \DOMText('Error');
                $persNameId = "Error";
        }

        $resp->appendChild($respText);
        $persName->appendChild($persNameText);
        $persName->setAttribute("xml:id", $persNameId);

        $statement->appendChild($resp);
        $statement->appendChild($persName);
        return $statement;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function buildRevisionDesc($doc, $entity) {
        /*
         * <revisionDesc>
         *    <!-- le fichier TEI doit consigner la liste des principales étapes de travail -->
         *    <!--  <change when="2017-07-30" who="#LDupont">première transcription</change>-->
         *    <change when-iso="2017-07-31/2017-08-02" who="#MDurand #tata">transcription</change>
         *    <change when="2017-08-01" who="#FClavaud">validation</change>
         *    <change when="2017-08-03" who="#KPineau">export</change>
         * </revisionDesc>
         */

        $revisionDesc = $doc->createElement('revisionDesc');

        $change = $doc->createElement('change');
        $change->setAttribute('when', $entity->getUpdateDate()->format('Y-m-d'));
        $change->setAttribute('who', $entity->getUpdateUser()->getId());
        $change->appendChild(new \DOMText('validation'));
        $revisionDesc->appendChild($change);

        return $revisionDesc;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function buildSourceDesc($doc, $entity) {
        /*
         * <!-- description du document édité -->
         * <sourceDesc>
         *     <msDesc>
         *        <msIdentifier>
         *           <institution>Archives nationales</institution>
         *           <collection>Minutier central des notaires de Paris</collection>
         *           <idno>MC/ET/XLVIII/1820, minute du 20 janvier 1921 (dépôt du testament de Fernand
         *              Lucien Jules Melchior Chatin)</idno>
         *           <msName>Testament de Fernand Lucien Jules Melchior Chatin (31 juillet
         *              1914)</msName>
         *        </msIdentifier>
         *        <msContents>
         *           <summary>
         *              <p><date when="1914-07-31" type="willDate">1914, 31 juillet</date>. <placeName
         *                    type="willPlace">Paris</placeName>.</p>
         *
         *              <bibl/>
         *           </summary>
         *        </msContents>
         *        <physDesc>
         *           <objectDesc>
         *              <supportDesc>
         *                 <support>papier à lettre de deuil avec adresse imprimée</support>
         *                 <extent>4 pages, <dimensions unit="cm">
         *                       <height>17,2</height>
         *                       <width>11,2</width>
         *                    </dimensions></extent>
         *              </supportDesc>
         *           </objectDesc>
         *           <handDesc>
         *              <p>encre</p>
         *           </handDesc>
         *        </physDesc>
         *     </msDesc>
         * </sourceDesc>
         */

        $sourceDesc = $doc->createElement('sourceDesc');
        $msDesc = $doc->createElement('msDesc');

        /* -- msIdentifier -- */
        $msIdentifier = $doc->createElement('msIdentifier');
        $msDesc->appendChild($msIdentifier);

        $institution = $doc->createElement('institution');
        $collection = $doc->createElement('collection');
        if($entity->getWill()->getHostingOrganization()->getCode() == "AN") {
            $institution->appendChild(new \DOMText('Archives nationales'));
            $collection->appendChild(new \DOMText('Minutier central des notaires de Paris'));
        } elseif($entity->getWill()->getHostingOrganization()->getCode() == "AD78") {
            $institution->appendChild(new \DOMText('Archives départementales des Yvelines'));
            $collection->appendChild(new \DOMText('TO DEFINE'));
        } else {
            $institution->appendChild(new \DOMText('Institution inconnue'));
            $collection->appendChild(new \DOMText('Collection inconnue'));
        }

        $idno = $doc->createElement('idno');
        $idno->appendChild(new \DOMText($entity->getWill()->getCallNumber().', minute du '.$entity->getWill()->getMinuteDateString().' (dépôt du testament de '.$entity->getWill()->getTestator()->getName().')'));

        $msName = $doc->createElement('msName');
        $msName->appendChild(new \DOMText('Testament de '.$entity->getWill()->getTestator()->getName().' ('.$entity->getWill()->getWillWritingDateString().')'));

        $msIdentifier->appendChild($institution);
        $msIdentifier->appendChild($collection);
        $msIdentifier->appendChild($idno);
        $msIdentifier->appendChild($msName);
        /* -- End : msIdentifier -- */

        /* -- msContents -- */
        $msContents = $doc->createElement('msContents');
        $summary = $doc->createElement('summary');

        if($entity->getWill()->getWillWritingDateString() != null && $entity->getWill()->getWillWritingPlaceNormalized() != null) {
            $pDate = $doc->createElement('p');

            if($entity->getWill()->getWillWritingDateString() != null) {
                $date = $doc->createElement('date');
                $date->appendChild(new \DOMText($entity->getWill()->getWillWritingDateString()));
                $pDate->appendChild($date);
                $pDate->appendChild(new \DOMText("."));
            }

            if($entity->getWill()->getWillWritingPlaceNormalized() != null) {
                $placeName = $doc->createElement('placeName');
                $placeName->appendChild(new \DOMText($entity->getWill()->getWillWritingPlaceNormalized()->getName()));
                $pDate->appendChild($placeName);
                $pDate->appendChild(new \DOMText("."));
            }

            $summary->appendChild($pDate);
        }

        $summary->appendChild($this->buildSummuryDetailedPersName($doc, $entity));

        $bibl = $doc->createElement('bibl');
        $summary->appendChild($bibl);

        $msContents->appendChild($summary);
        /* -- End : msContents -- */

        /* -- physDesc -- */
        $physDesc = $doc->createElement('physDesc');

        $objectDesc = $doc->createElement('objectDesc');
        $supportDesc = $doc->createElement('supportDesc');
        // -> Support
        $support = $doc->createElement('support');
        if($entity->getWill()->getPagePhysDescSupport() != null) {
            $support->appendChild(new \DOMText($entity->getWill()->getPagePhysDescSupport()));
        }
        $supportDesc->appendChild($support);

        // -> Extent
        $extent = $doc->createElement('extent');
        $extent->appendChild(new \DOMText('X pages'));

        // --> Dimensions
        $dimensions = $doc->createElement('dimensions');
        $dimensions->setAttribute('unit', 'cm');
        // ---> Height
        $height = $doc->createElement('height');
        if($entity->getWill()->getPagePhysDescHeight() != null) {
            $height->appendChild(new \DOMText($entity->getWill()->getPagePhysDescHeight()));
        }
        $dimensions->appendChild($height);
        // ---> Width
        $width = $doc->createElement('width');
        if($entity->getWill()->getPagePhysDescWidth() != null) {
            $width->appendChild(new \DOMText($entity->getWill()->getPagePhysDescWidth()));
        }
        $dimensions->appendChild($width);
        $objectDesc->appendChild($supportDesc);
        $physDesc->appendChild($objectDesc);

        // -> HandDesc
        $handDesc = $doc->createElement('handDesc');
        if($entity->getWill()->getPagePhysDescHand() != null) {
            $pHand = $doc->createElement('p');
            $pHand->appendChild(new \DOMText($entity->getWill()->getPagePhysDescHand()));
            $handDesc->appendChild($pHand);
        }

        $physDesc->appendChild($handDesc);

        $msDesc->appendChild($physDesc);
        /* -- END : physDesc -- */

        $sourceDesc->appendChild($msDesc);
        return $sourceDesc;
    }



    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function buildSummuryDetailedPersName($doc, $entity) {
        /*
         * <p>
         *      <term type="willType">testament olographe</term>
         *      de
         *      <persName ref="#FLChatin">
         *          <forename>Fernand</forename>
         *          <forename>Lucien</forename>
         *          <forename>Jules</forename>
         *          <forename>Melchior</forename>
         *          <surname>Chatin</surname>
         *      </persName>,
         *      mort pour la France à
         *      <placeName ref="#pl-056" type="willAuthorDeathPlace">Maroeuil (Pas-de-Calais)</placeName>,
         *      le <date when="1915-01-10" type="willAuthorDeathDate">10 janvier 1915</date>.
         * </p>
         *
         */

        $pSummary = $doc->createElement('p');

        /* -- Building phrase -- */
        $term = $doc->createElement('term');
        $term->setAttribute('type', 'willType');
        $term->appendChild(new \DOMText('testament olographe'));
        $pSummary->appendChild($term);

        $pSummary->appendChild(new \DOMText(" de "));

        $pSummary->appendChild($this->buildName($doc, $entity));

        $deathContextElements = $this->buildDeathContext($doc, $entity);
        foreach($deathContextElements as $deathContext) {$pSummary->appendChild($deathContext);}

        return $pSummary;
    }

    /**
     * @param $doc \DOMDocument
     * @param $entity \AppBundle\Entity\Entity
     * @return \DOMElement
     */
    private function buildName($doc, $entity) {
        /*
         * <persName ref="#FLChatin">
         *          <forename>Fernand</forename>
         *          <forename>Lucien</forename>
         *          <forename>Jules</forename>
         *          <forename>Melchior</forename>
         *          <surname>Chatin</surname>
         *  </persName>
         */
        $arrayFornames = explode(' ', $entity->getWill()->getTestator()->getFirstnames());

        $persName = $doc->createElement('persName');
        $persName->setAttribute('ref', '#pers-'.$entity->getWill()->getTestator()->getId());

        foreach($arrayFornames as $forname) {
            $fornameDoc = $doc->createElement('forname');
            $fornameDoc->appendChild(new \DOMText($forname));
            $persName->appendChild($fornameDoc);
        }

        $surname = $doc->createElement('surname');
        $surname->appendChild(new \DOMText($entity->getWill()->getTestator()->getSurname()));
        $persName->appendChild($surname);

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
         * mort pour la France à
         * <placeName ref="#pl-056">Maroeuil (Pas-de-Calais)</placeName>,
         * le <date when="1915-01-10" type="willAuthorDeathDate">10 janvier 1915</date>.
         */


        $elements = array();

        if($entity->getWill()->getTestator()->getDeathMention() != null) {
            $elements[] = new \DOMText(", ".$entity->getWill()->getTestator()->getDeathMention());
        } else {
            $elements[] = new \DOMText(", mort");
        }

        if($entity->getWill()->getTestator()->getPlaceOfDeathNormalized() != null) {
            $elements[] = new \DOMText(" à ");
            $placeName = $doc->createElement('placeName');
            $placeName->setAttribute('ref', '#pl-'.$entity->getWill()->getTestator()->getPlaceOfDeathNormalized()->getId());
            $placeName->appendChild(new \DOMText($entity->getWill()->getTestator()->getPlaceOfDeathNormalized()->getName()));
            $elements[] = $placeName;
        }
        if($entity->getWill()->getTestator()->getPlaceOfDeathNormalized() != null && $entity->getWill()->getTestator()->getDateOfDeathString() != null) {
            $elements[] = new \DOMText(", ");
        }
        if($entity->getWill()->getTestator()->getDateOfDeathString() != null) {
            $elements[] = new \DOMText(" le ");

            $date = $doc->createElement('date');
            $date->setAttribute('when', $entity->getWill()->getTestator()->getDateOfDeathString());
            $date->setAttribute('type', 'willAuthorDeathDate');
            $date->appendChild(new \DOMText($entity->getWill()->getTestator()->getDateOfDeathString()));
            $elements[] = $date;
        }
        $elements[] = new \DOMText(".");

        return $elements;
    }

}