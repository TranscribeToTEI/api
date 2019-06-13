<?php

namespace AppBundle\Services\XML\Builder\Wills;

use AppBundle\Services\Entity;
use AppBundle\Services\ResourceI;
use AppBundle\Services\Versioning;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use UserBundle\Entity\User;

class Header
{
    private $em;
    private $functions;
    private $entity;
    private $logger;

    public function __construct(EntityManager $em, Functions $functions, Entity $entity, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->functions = $functions;
        $this->entity = $entity;
        $this->logger = $logger;
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
         *  <teiHeader>
         *      <fileDesc>
         *          <titleStmt>
         *              <title>Title</title>
         *          </titleStmt>
         *          <publicationStmt>
         *              <p>Publication Information</p>
         *          </publicationStmt>
         *          <sourceDesc></sourceDesc>
         *      </fileDesc>
         *      <revisionDesc></revisionDesc>
         *  </teiHeader>
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

        $publisher1 = $doc->createElement('publisher');
        $publiOrgName1 = $doc->createElement('orgName');
        $publiOrgName1->appendChild(new \DOMText("Université de Cergy-Pontoise"));
        $publisher1->appendChild($publiOrgName1);
        $publicationStmt->appendChild($publisher1);

        $publisher2 = $doc->createElement('publisher');
        $publiOrgName2 = $doc->createElement('orgName');
        $publiOrgName2->appendChild(new \DOMText("Archives nationales"));
        $publisher2->appendChild($publiOrgName2);
        $publicationStmt->appendChild($publisher2);

        $publisher3 = $doc->createElement('publisher');
        $publiOrgName3 = $doc->createElement('orgName');
        $publiOrgName3->appendChild(new \DOMText("Archives départementales des Yvelines"));
        $publisher3->appendChild($publiOrgName3);
        $publicationStmt->appendChild($publisher3);

        $publisher4 = $doc->createElement('publisher');
        $publiOrgName4 = $doc->createElement('orgName');
        $publiOrgName4->appendChild(new \DOMText("École nationale des chartes"));
        $publisher4->appendChild($publiOrgName4);
        $publicationStmt->appendChild($publisher4);

        $pubPlace = $doc->createElement('pubPlace');
        $pubPlace->appendChild(new \DOMText("Paris"));
        $publicationStmt->appendChild($pubPlace);

        $date = $doc->createElement('date');
        //$date->setAttribute("when", date("Y-m-d"));
        $date->setAttribute("type", "otherDate");
        $publicationStmt->appendChild($date);

        $availability = $doc->createElement('availability');
        $licence = $doc->createElement('licence');
        $licence->appendChild(new \DOMText("Licence CC-BY (à revoir)"));
        $availability->appendChild($licence);
        $publicationStmt->appendChild($availability);
        /* -- End : Publication Statement -- */

        /* -- Source Description -- */
        $fileDesc->appendChild($this->buildSourceDesc($doc, $entity));
        /* -- END = Source Description -- */

        /* -- Profile Description -- */
        $profileStmt = $doc->createElement('publicationStmt');
        $profileStmt = $fileDesc->appendChild($profileStmt);

        $creation = $doc->createElement('creation');
        $creation->appendChild(new \DOMText("Fichier produit à partir des données saisies avant import initial dans la plate-forme, et de la transcription du testament qui a été produite directement dans la plate-forme, converties automatiquement en TEI"));
        $profileStmt->appendChild($creation);

        $langUsage = $doc->createElement('langUsage');
        $language = $doc->createElement('language');
        $language->appendChild(new \DOMText("français"));
        $language->setAttribute("ident", "fre");
        $langUsage->appendChild($language);
        $profileStmt->appendChild($langUsage);
        /* -- END = Profile Description -- */

        /* -- Encoding Description -- */
        $encodingDesc = $doc->createElement('encodingDesc');
        $encodingDesc = $fileDesc->appendChild($encodingDesc);

        $projectDesc = $doc->createElement('projectDesc');
        $p = $doc->createElement('p');
        $p->appendChild(new \DOMText("Description générale du projet de transcription collaborative etc."));
        $projectDesc->appendChild($p);
        $encodingDesc->appendChild($projectDesc);

        $editorialDecl = $doc->createElement('editorialDecl');
        $p1 = $doc->createElement('p');
        $p1->appendChild(new \DOMText(
            "L'édition est séquencée par document (le testament et sa ou ses éventuelles
               enveloppes) : pour chacun d'entre eux, un fichier TEI est produit. Ce choix est
               adapté à la fois à la nature du corpus (une collection homogène de documents) et aux
               modalités du travail collaboratif. L’édition liste également, et inclut,
               l’intégralité des images numériques des documents, qui ont été produites par les
               Archives nationales et les Archives départementales des Yvelines. A un document
               correspondent plusieurs image snumériques, lesquelles correspondent en général
               chacune à une page du testament ou à un des côtés (recto ou verso ) d'une enveloppe.
               Parfois cependant, le document ayant été relié et étant solidaire des autres pièces
               composant la minute, il a fallu produire deux images complémentaires pour la même
               page."));
        $editorialDecl->appendChild($p1);

        $p2 = $doc->createElement('p');
        $p2->appendChild(new \DOMText(
            "Chaque document a reçu un numéro d’ordre qui reflète l'ordre chronologique dans
               lequel le travail d'identification a été mené, sans autre signification."));
        $editorialDecl->appendChild($p2);

        $p3 = $doc->createElement('p');
        $p3->appendChild(new \DOMText(
            "Chaque testament est décrit par une brève analyse donnant : sa date de temps et de
               lieu ; le nom de son auteur, les circonstances et la date du décès du Poilu ; une
               description matérielle du testament (type de papier, pagination, dimensions, matière
               de l’écriture) ; sa cote aux Archives nationales ou aux Archives départementales des
               Yvelines."));
        $editorialDecl->appendChild($p3);

        $p4 = $doc->createElement('p');
        $p4->appendChild(
            new \DOMText(
            "Nous avons fait le choix de livrer au lecteur une édition lui permettant à la fois de
               lire une transcription imitative du texte, permettant de respecter le plus fidèlement
               possible l’écriture et la mise en page du Poilu, et de rendre compte ainsi de la
               matérialité du texte ; et une édition du texte définitif, modernisée (orthographe) et
               normalisée (harmonisation de l’écriture des dates, de la ponctuation et de la
               présentation, et résolution des abréviations correction des fautes de syntaxe), qui
               permet une lecture plus fluide. Pour cette dernière, nous avons suivi les règles
               d’édition de l’École des Chartes ("));

            $bibl = $doc->createElement('bibl');
            $p4->appendChild($bibl);

            $author1 = $doc->createElement('author');
            $bibl->appendChild($author1);
            $author1->appendChild(new \DOMText("Nougaret (Christine)"));

            $bibl->appendChild(new \DOMText(","));

            $author2 = $doc->createElement('author');
            $bibl->appendChild($author2);
            $author2->appendChild(new \DOMText("Parinet (Élisabeth)"));

            $bibl->appendChild(new \DOMText(","));

            $respStmt = $doc->createElement('respStmt');
            $bibl->appendChild($respStmt);
            $persName = $doc->createElement('persName');
            $persName->appendChild(new \DOMText("Parinet (Élisabeth)"));
            $respStmt->appendChild($persName);
            $resp = $doc->createElement('resp');
            $resp->appendChild(new \DOMText("(collab.)"));
            $respStmt->appendChild($resp);

            $bibl->appendChild(new \DOMText(","));

            $title = $doc->createElement('title');
            $bibl->appendChild($title);
            $title->appendChild(new \DOMText("L’édition critique des textes contemporains, XIXe-XXIe siècle"));
            $title->setAttribute("level", "a");

            $bibl->appendChild(new \DOMText(". Paris : École nationale des chartes, 2015."));


        $p4->appendChild(new \DOMText("."));
        $editorialDecl->appendChild($p4);

        $interpretation = $doc->createElement('interpretation');
        $pInterpretation = $doc->createElement('p');
        $pInterpretation->appendChild(new \DOMText(
            "Les expressions évoquant la vision de la mort et celles dénotant l'état d'esprit
                  des testateurs ont fait l'objet d'un balisage renvoyant à une grille d'analyse
                  contenant deux micro-vocabulaires."));
        $interpretation->appendChild($pInterpretation);
        $encodingDesc->appendChild($interpretation);

        $normalization = $doc->createElement('normalization');
        $pnormalization = $doc->createElement('p');
        $normalization->appendChild($pnormalization);
        $encodingDesc->appendChild($normalization);

        $hyphenation = $doc->createElement('hyphenation');
        $phyphenation = $doc->createElement('p');
        $hyphenation->appendChild($phyphenation);
        $encodingDesc->appendChild($hyphenation);

        $punctuation = $doc->createElement('punctuation');
        $ppunctuation = $doc->createElement('p');
        $punctuation->appendChild($ppunctuation);
        $encodingDesc->appendChild($punctuation);

        $hyphenation = $doc->createElement('hyphenation');
        $phyphenation = $doc->createElement('p');
        $hyphenation->appendChild($phyphenation);
        $encodingDesc->appendChild($hyphenation);

        $encodingDesc->appendChild($editorialDecl);

        $samplingDecl = $doc->createElement('samplingDecl');
        $pSamplingDecl = $doc->createElement('p');
        $pSamplingDecl->appendChild(new \DOMText(
            "L’édition porte sur les testaments olographes et leurs enveloppes éventuelles, si
               elles sont de la main du Poilu. Seules les parties du texte de la main du testateur
               sont transcrites. La présente édition ne concerne donc pas les éléments textuels
               ajoutés au testament lors de son ouverture au tribunal (pagination, paraphes,
               signature du juge...) ni les autres mentions hors teneur. Seul est donc édité le
               texte autographe tel que délimité par le bâtonnage du juge."));
        $samplingDecl->appendChild($pSamplingDecl);
        $encodingDesc->appendChild($samplingDecl);

        $variantEncoding = $doc->createElement('variantEncoding');
        $variantEncoding->setAttribute("method", "parallel-segmentation");
        $variantEncoding->setAttribute("location", "internal");
        $encodingDesc->appendChild($variantEncoding);

        $appInfo = $doc->createElement('appInfo');
        $application = $doc->createElement('application');
        $application->setAttribute("version", "1.0");
        $application->setAttribute("ident", "TranscribeToTEI");
        $label = $doc->createElement('label');
        $label->appendChild(new \DOMText("Transcribe To TEI"));
        $ref = $doc->createElement('ref');
        $ref->appendChild(new \DOMText("https://github.com/TranscribeToTEI"));
        $ref->setAttribute("target", "https://github.com/TranscribeToTEI");
        $application->appendChild($label);
        $application->appendChild($ref);
        $appInfo->appendChild($application);
        $encodingDesc->appendChild($appInfo);
        /* -- END = Encoding Description -- */

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
        $identificationUsersString = $entity->getWill()->getIdentificationUsers();

        if(strpos("|", $identificationUsersString) == false) {
            $identificationUsers = explode("|", $identificationUsersString);
        } else {
            $identificationUsers = [$identificationUsersString];
        }

        foreach($identificationUsers as $identificationUser) {
            $respStmtIdentifier = $this->buildRespStmt($doc, trim($identificationUser));
            $titleStmt->appendChild($respStmtIdentifier);
        }

        return $titleStmt;
    }



    /**
     * @param $doc \DOMDocument
     * @param $identificationUser string
     * @return \DOMNode
     */
    private function buildRespStmt($doc, $identificationUser) {
        $statement = $doc->createElement('respStmt');

        $resp = $doc->createElement('resp');
        $resp->appendChild(new \DOMText("Identification du testament"));
        $statement->appendChild($resp);

        $persName = $doc->createElement('persName');
        $persName->appendChild(new \DOMText($identificationUser));
        $persName->setAttribute("xml:id", $this->functions->getIdFromName($identificationUser));
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
         *    <change when-iso="2017-07-31/2017-08-02" who="#MDurand #tata">transcription</change>
         *    <change when="2017-08-01" who="#FClavaud">validation</change>
         * </revisionDesc>
         */

        $revisionDesc = $doc->createElement('revisionDesc');

        $whoValidator = "";
        $whoTranscription = "";

        $datesValidator = [];
        $datesTranscription = [];
        $firstWhenValidator = "";
        $lastWhenValidator = "";
        $firstWhenTranscription = "";
        $lastWhenTranscription = "";

        foreach($this->entity->getContributors($entity) as $contributor) {
            /** @var User $user */
            $user = $contributor['user'];
            if(in_array("ROLE_ADMIN", $user->getRoles())) {
                if($whoValidator != "") { $whoValidator .= " "; }
                $whoValidator .= "#".$this->functions->getIdFromName($user->getName());
                $datesValidator = array_merge($datesValidator, $contributor["dates"]);
            } else {
                if($whoTranscription != "") { $whoTranscription .= " "; }
                $whoTranscription .= "#".$this->functions->getIdFromName($user->getName());
                $datesTranscription = array_merge($datesTranscription, $contributor["dates"]);
            }
        }

        usort($datesValidator, array($this, "cmp"));
        usort($datesTranscription, array($this, "cmp"));

        if(count($datesValidator) > 0) {
            $firstWhenValidator = $datesValidator[0]->format('Y-m-d');
            if (count($datesValidator) > 1) {
                $lastWhenValidator = $datesValidator[count($datesValidator) - 1]->format('Y-m-d');
            } else {
                $lastWhenValidator = $datesValidator[0]->format('Y-m-d');
            }
        }

        if(count($datesTranscription) > 0) {
            $firstWhenTranscription = $datesTranscription[0]->format('Y-m-d');
            if (count($datesTranscription) > 1) {
                $lastWhenTranscription = $datesTranscription[count($datesTranscription) - 1]->format('Y-m-d');
            } else {
                $lastWhenTranscription = $datesTranscription[0]->format('Y-m-d');
            }
        }

        if($whoTranscription != "") {
            $change1 = $doc->createElement('change');
            if ($firstWhenValidator == $lastWhenValidator) {
                $change1->setAttribute('when', $firstWhenValidator);
            } else {
                $change1->setAttribute('when-iso', $firstWhenValidator . "/" . $lastWhenValidator);
            }
            $change1->setAttribute('who', $whoTranscription);
            $change1->appendChild(new \DOMText('transcription'));
            $revisionDesc->appendChild($change1);
        }

        if($whoValidator != "") {
            $change2 = $doc->createElement('change');
            if ($firstWhenTranscription == $lastWhenTranscription) {
                $change2->setAttribute('when', $firstWhenTranscription);
            } else {
                $change2->setAttribute('when-iso', $firstWhenValidator . "/" . $lastWhenTranscription);
            }
            $change2->setAttribute('who', $whoValidator);
            $change2->appendChild(new \DOMText('validation'));
            $revisionDesc->appendChild($change2);
        }
        
        return $revisionDesc;
    }

    function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
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
            <!-- description du document édité -->
            <msDesc>

               <msIdentifier>
                  <!-- TODO voir AD 78 pour l'alternative AD78 -->
                  <institution>Archives nationales</institution>
                  <collection>Minutier central des notaires de Paris</collection>

                  <idno>MC/ET/XLVIII/1820, minute du 20 janvier 1921 (dépôt du testament de Fernand
                     Lucien Jules Melchior Chatin)</idno>
                  <msName>Testament de Fernand Lucien Jules Melchior Chatin (31 juillet
                     1914)</msName>

               </msIdentifier>
               <msContents>
                  <summary>
                     <p><date when="1914-07-31" type="willDate">1914, 31 juillet</date>. <placeName type="willPlace">Paris</placeName>.</p>
                     <p><term type="willType">testament olographe</term> de <persName ref="FirstWorldWarWills-ListOfPersons.xml#p-0001"><forename>Fernand</forename>
                           <forename>Lucien</forename>
                           <forename>Jules</forename>
                           <forename>Melchior</forename>
                           <surname>Chatin</surname></persName>, mort pour la France à <placeName ref="FirstWorldWarWills-ListOfPlaces#pl-0056" type="willAuthorDeathPlace">Maroeuil (Pas-de-Calais)</placeName>, le <date when="1915-01-10" type="willAuthorDeathDate">10 janvier 1915</date>.</p>
                  </summary>


               </msContents>

               <!-- <physDesc><p>Papier à lettre de deuil avec adresse imprimée, 4 pages, dim. 17,2 x 11,2 cm,
                 encre.</p></physDesc>-->
               <physDesc>

                  <objectDesc>
                     <supportDesc>
                        <support>Testament : papier à lettre de deuil avec adresse imprimée. Enveloppe : blbalba</support>
                        <extent>4 pages, <dimensions unit="cm">
                              <height>17,2</height>
                              <width>11,2</width>
                           </dimensions></extent>
                     </supportDesc>

                  </objectDesc>
                  <handDesc>
                     <p>Testament: Encre. Enveloppe: Crayon.</p>
                  </handDesc>
               </physDesc>
               <history>
                  <provenance><orgName>étude notariale CCX (CRPCEN 75123)</orgName></provenance> -> CHAMP PAS OBLIGATOIRE, IL N'EST PAS LA POUR LES AD78
                    -> Puis il y a les infos sur l'étude à rajouter (notaryNumber)
               </history>
            </msDesc>
            </sourceDesc>


            POUR LES INFO SUR TESTATEUR : AFFICHER LES MEMES INFORMATIONS QUE LE BLOC DANS L'APPLI DE TRANSCRIPTION
            POUR LES DESCRIPTIONS PHYSIQUE : ON REPETE LES INFOS DE L'ENVELOPPE DANS EXTENT. ET POUR LE HANDDESC : ON ECRIT: "ENVELOPPE: CRAYON. TESTAMENT: ENCRE"

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
            $collection->appendChild(new \DOMText('Archives notariales'));
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