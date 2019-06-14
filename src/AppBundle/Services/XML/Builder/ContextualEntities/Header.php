<?php

namespace AppBundle\Services\XML\Builder\ContextualEntities;

use AppBundle\Services\Entity;
use AppBundle\Services\ResourceI;
use AppBundle\Services\Versioning;
use AppBundle\Services\XML\Builder\Wills\Functions;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use UserBundle\Entity\User;

class Header
{
    private $em;
    private $functions;
    private $logger;

    public function __construct(EntityManager $em, Functions $functions, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->functions = $functions;
        $this->logger = $logger;
    }

    /**
     * @param $doc \DOMDocument
     * @return \DOMElement
     */
    public function build($doc)
    {
        $teiHeader = $doc->createElement('teiHeader');

        $fileDesc = $doc->createElement('fileDesc');
        $fileDesc = $teiHeader->appendChild($fileDesc);

        /* -- Title Statement -- */
        $titleStmt = $doc->createElement('titleStmt');
        $title = $doc->createElement('title');
        $title->appendChild(new \DOMText("À définir"));
        $titleStmt->appendChild($title);


        $fileDesc->appendChild($titleStmt);

        return $teiHeader;
    }

}