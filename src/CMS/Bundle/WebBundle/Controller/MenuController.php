<?php

namespace CMS\Bundle\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller
{
    public function indexAction($slug)
    {
        return $this->render('CMSWebBundle:Menu:index.html.twig', array('name' =>'Hai'));
    }
}
