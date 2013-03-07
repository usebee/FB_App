<?php

namespace CMS\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CMS\Bundle\AdminBundle\Entity\Menu;
use CMS\Bundle\AdminBundle\Form\MenuType;
use CMS\Bundle\AdminBundle\Model\ModelMenu;

/**
 * Menu controller.
 *
 */
class MenuController extends Controller
{
    /**
     * index controller
     *
     * @param type $page
     *
     * @return type
     */
    public function indexAction($page)
    {
        return $this->render('CMSAdminBundle:Menu:index.html.twig', array(
        ));
    }

}