<?php

namespace CMS\Bundle\WebBundle\Controller;

use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {
	public function indexAction($path) {
		// echo $path;exit;
		$menus = array (
				'home' => 'Home',
				'project' => 'Project',
				'service' => 'Service',
				'download' => 'Downloads',
				'about' => 'About',
				'contact' => 'Contact Us' 
		);		
		$active = null;
		foreach ( $menus as $key => $value ) {
			if ($path == $key) {
				$active = $key;
			}
		}
		
		return $this->render ( 'CMSWebBundle:Default:index.html.twig', array (
				'menus' => $menus,
				'active' => $active 
		) );
	}
}
