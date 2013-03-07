<?php

namespace CMS\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * the Rss controller
 */
class RssController extends Controller {
	
	/**
	 * List RSS 
	 *
	 * @return type
	 */
	public function indexAction($lang = null) {

		$feed = $this->get('fkr_simple_pie.rss');
		$feed->set_feed_url('https://itunes.apple.com/vn/rss/topfreeapplications/limit=10/xml');
		//$feed->set_feed_url('http://vnexpress.net/rss/gl/xa-hoi.rss');
		$feed->enable_order_by_date(false);
		$feed->set_cache_location($_SERVER['DOCUMENT_ROOT'] . '/cache');
		$feed->init();
		$result = $feed->get_items(0,2);
		/* foreach ($result as $item){
			echo '<pre/>';
			print_R($item->get_content());exit;
			
		}
		exit; */
		return $this->render ( 'CMSAdminBundle:Rss:index.html.twig', array ('result'=>$result) );
	}
	
	/**
	 * Add Rss 
	 *
	 * @return type
	 */
	public function addAction($lang = null) {
	
		return $this->render ( 'CMSAdminBundle:Rss:add.html.twig', array () );
	}
}
