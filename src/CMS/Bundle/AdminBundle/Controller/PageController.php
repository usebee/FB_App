<?php

namespace CMS\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use CMS\Bundle\AdminBundle\Entity\Page;
use CMS\Bundle\AdminBundle\Entity\PageLanguage;
use CMS\Bundle\AdminBundle\Form\PageType;

/**
 * Page controller.
 */
class PageController extends Controller {
	
	/**
	 * listing page
	 *
	 * @param type $page        	
	 *
	 * @param type $lang        	
	 *
	 * @return type
	 */
	public function indexAction($page, $lang) {
		// get list language
		$langList = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:Language" )->findAll ();

		if (is_null ( $lang )) {
			foreach ( $langList as $langData ) {
				$isDefault = $langData->getIsDefault ();
				if ($isDefault == 1) {
					$lang = $langData->getId ();
					break;
				}
			}
		}
		
		$currentLanguage = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:Language" )->find ( $lang );

		$total = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:Page" )->getTotal ();
		
		$perPage = $this->container->getParameter ( 'per_item_page' );
		$lastPage = ceil ( $total / $perPage );
		$previousPage = $page > 1 ? $page - 1 : 1;
		$nextPage = $page < $lastPage ? $page + 1 : $lastPage;
		
		$entities = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:Page" )->getList ( $perPage, ($page - 1) * $perPage );
		
		foreach ( $entities as $thePage ) {
			$thePage->setLanguage ( $currentLanguage );
		}
		
		// var_dump($entities[1]->getPageLanguages()->toArray()); die;
		
		return $this->render ( 'CMSAdminBundle:Page:index.html.twig', array (
				'entities' => $entities,
				'lastPage' => $lastPage,
				'previousPage' => $previousPage,
				'currentPage' => $page,
				'nextPage' => $nextPage,
				'total' => $total,
				'lang' => intval ( $lang ),
				'langList' => $langList 
		) );
	}
	
	/**
	 * show form create
	 *
	 * @return type
	 */
	public function newAction() {
		$entity = new Page ();
		
		// get list language
		$langList = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:Language" )->findAll ();
		
		if (is_array ( $langList )) {
			foreach ( $langList as $language ) {
				$pageLanguage = new PageLanguage ();
				$pageLanguage->setLanguage ( $language );
				$pageLanguage->setPage ( $entity );
				
				if ($language->getIsDefault ()) {
					$defaultLanguage = $language;
				}
				$entity->addPageLanguage ( $pageLanguage );
			}
		}
		
		$pageType = new PageType ( $this->getTemplates () );
		$form = $this->createForm ( $pageType, $entity );
		
		if ($this->getRequest ()->isMethod ( 'POST' )) {
			$form->bind ( $this->getRequest () );
			
			if ($form->isValid ()) {
				$entityManager = $this->getDoctrine ()->getEntityManager ();
				foreach ( $entity->getPageLanguages () as $pageLanguage ) {
					$plName = $pageLanguage->getName ();
					if (empty ( $plName )) {
						$entity->removePageLanguage ( $pageLanguage );
					}
				}
				$this->resetUnique ( $entity );
				
				$entityManager->persist ( $entity );
				
				$entityManager->flush ();
				
				// update alias for each $pageLanguage
				$this->getDoctrine ()->getRepository ( "CMSAdminBundle:PageLanguage" )->updateAlias ( $entity );
				
				$this->getRequest ()->getSession ()->getFlashBag ()->add ( 'cms_flash_success', 'Page insert successfull!' );
				
				return $this->redirect ( $this->generateUrl ( 'admin_page' ) );
			} else {
				$this->getRequest ()->getSession ()->getFlashBag ()->add ( 'cms_flash_error', 'Form invalid' );
			}
		}
		
		// get Medias
		$optMedias = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:Media" )->findAll ();
		
		return $this->render ( 'CMSAdminBundle:Page:new.html.twig', array (
				'entity' => $entity,
				'form' => $form->createView (),
				'langList' => $langList,
				'defaultLanguage' => $defaultLanguage,
				'optMedias' => $optMedias,
				'mediaPath' => $this->container->getParameter ( 'upload' ) 
		) );
	}
	
	/**
	 * edit item
	 *
	 * @param type $id        	
	 *
	 * @return type
	 */
	public function editAction($id) {
		$entity = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:Page" )->find ( $id );
		
		if (! $entity) {
			// go to page index with error
			$this->getRequest ()->getSession ()->getFlashBag ()->add ( 'cms_flash_error', 'Could not find page with id ' . $id );
			
			return $this->redirect ( $this->generateUrl ( 'admin_page' ) );
		}
		
		// get list language
		$langList = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:Language" )->findAll ();
		
		if (is_array ( $langList )) {
			foreach ( $langList as $language ) {
				if (! $entity->hasLanguage ( $language )) {
					$pageLanguage = new PageLanguage ();
					$pageLanguage->setLanguage ( $language );
					$pageLanguage->setPage ( $entity );
					$entity->addPageLanguage ( $pageLanguage );
				}
				if ($language->getIsDefault ()) {
					$defaultLanguage = $language;
				}
			}
		}
		
		$pageType = new PageType ( $this->getTemplates () );
		$form = $this->createForm ( $pageType, $entity );
		
		if ($this->getRequest ()->iCMSethod ( 'POST' )) {
			$form->bind ( $this->getRequest () );
			
			if ($form->isValid ()) {
				$entityManager = $this->getDoctrine ()->getEntityManager ();
				foreach ( $entity->getPageLanguages () as $pageLanguage ) {
					$plName = $pageLanguage->getName ();
					if (empty ( $plName )) {
						$entity->removePageLanguage ( $pageLanguage );
						$entityManager->remove ( $pageLanguage );
					}
				}
				$this->resetUnique ( $entity );
				
				$entityManager->persist ( $entity );
				
				$entityManager->flush ();
				
				// update alias for each $pageLanguage
				$this->getDoctrine ()->getRepository ( "CMSAdminBundle:PageLanguage" )->updateAlias ( $entity );
				
				$this->getRequest ()->getSession ()->getFlashBag ()->add ( 'cms_flash_success', 'Page edit successfull!' );
				
				return $this->redirect ( $this->generateUrl ( 'admin_page' ) );
			} else {
				$this->getRequest ()->getSession ()->getFlashBag ()->add ( 'cms_flash_error', 'Form invalid' );
			}
		}
		
		// get Medias
		$optMedias = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:Media" )->findAll ();
		
		return $this->render ( 'CMSAdminBundle:Page:edit.html.twig', array (
				'entity' => $entity,
				'form' => $form->createView (),
				'langList' => $langList,
				'defaultLanguage' => $defaultLanguage,
				'optMedias' => $optMedias,
				'mediaPath' => $this->container->getParameter ( 'upload' ) 
		) );
	}
	
	/**
	 * Function to delete page
	 *
	 * @param type $id
	 *        	The id of Page
	 *        	
	 * @return type
	 */
	public function deleteAction($id) {
		$result = $this->getDoctrine ()->getRepository ( "CMSAdminBundle:PageLanguage" )->deleteByIds ( array (
				$id 
		) );
		
		if ($result) {
			$this->getRequest ()->getSession ()->getFlashBag ()->add ( 'cms_flash_success', 'Page deleted successfull!' );
		} else {
			$this->getRequest ()->getSession ()->getFlashBag ()->add ( 'cms_flash_error', 'Can not delete the Page' );
		}
		
		// set referrer redirect
		$referrer = $this->getRequest ()->server->get ( 'HTTP_REFERER' );
		
		if (! $referrer) {
			return $this->redirect ( $this->generateUrl ( 'admin_page' ) );
		} else {
			return $this->redirect ( $referrer );
		}
	}
	
	/**
	 * up action
	 *
	 * @param type $id        	
	 *
	 * @return type
	 */
	public function upAction($id) {
		$em = $this->getDoctrine ()->getEntityManager ();
		$repo = $em->getRepository ( 'CMSAdminBundle:Page' );
		$oCat = $repo->findOneById ( $id );
		if ($oCat->getParent ()) {
			$repo->moveUp ( $oCat );
		}
		
		return $this->redirect ( $this->getRequest ()->headers->get ( 'referer' ) );
	}
	
	/**
	 * down action
	 *
	 * @param type $id        	
	 *
	 * @return type
	 */
	public function downAction($id) {
		$em = $this->getDoctrine ()->getEntityManager ();
		$repo = $em->getRepository ( 'CMSAdminBundle:Page' );
		$oCat = $repo->findOneById ( $id );
		if ($oCat->getParent ()) {
			$repo->moveDown ( $oCat );
		}
		
		return $this->redirect ( $this->getRequest ()->headers->get ( 'referer' ) );
	}
	
	/**
	 * Render templates configs
	 *
	 * @return array
	 */
	public function getTemplates() {
		$templates = array ();
		$config = $this->container->getParameter ( 'templates' );
		for($i = 0; $i < $config; $i ++) {
			$templates [$i + 1] = 'Template ' . ($i + 1);
		}
		
		return $templates;
	}
	
	/**
	 * Reset the unique is_home and is_showreel of the Page
	 *
	 * @param \CMS\Bundle\AdminBundle\Entity\Page $page        	
	 */
	private function resetUnique(\CMS\Bundle\AdminBundle\Entity\Page $page) {
		if ($page->getIsHome ()) {
			$this->getDoctrine ()->getRepository ( "CMSAdminBundle:Page" )->resetIsHome ();
			$page->setIsHome ( 1 );
		}
		
		if ($page->getIsShowreel ()) {
			$this->getDoctrine ()->getRepository ( "CMSAdminBundle:Page" )->resetIsShowreel ();
			$page->setIsShowreel ( 1 );
		}
	}
}
