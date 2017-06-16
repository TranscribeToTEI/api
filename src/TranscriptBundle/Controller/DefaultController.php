<?php

namespace TranscriptBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TranscriptBundle:Default:index.html.twig');
    }
}
