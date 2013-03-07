<?php

namespace CMS\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CMS\Bundle\AdminBundle\Entity\Article;
use CMS\Bundle\AdminBundle\Entity\ArticleLanguage;

use CMS\Bundle\AdminBundle\Form\ArticleType;


/**
 * Article controller.
 *
 */
class ArticleController extends Controller
{

    /**
     * @param type $page page
     * @param type $lang lang
     *
     * @return type
     */
    public function indexAction($page, $lang)
    {
        $repLang = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:Language");

        $repPageLang = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:PageLanguage");
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
        $currentLanguage = $this->getDoctrine()
            ->getRepository("CMSAdminBundle:Language")
            ->find($lang);

        $total = $this->getDoctrine()
            ->getRepository("CMSAdminBundle:Article")
            ->getTotal();

        $perPage = $this->container->getParameter('per_item_page');;
        $lastPage = ceil($total / $perPage);
        $previousPage = $page > 1 ? $page - 1 : 1;
        $nextPage = $page < $lastPage ? $page + 1 : $lastPage;

        $entities = $this->getDoctrine()
            ->getRepository("CMSAdminBundle:Article")
            ->getList($perPage, ($page - 1) * $perPage);

        foreach ($entities as $theArticle) {
        	
            $theArticle->setLanguage($currentLanguage);
            //get page lang by id page & id lang
            $entPage = $theArticle->getPage();
            if (is_object($entPage)) {
                $entPageLang = $repPageLang->findByIdPageAndIdLanguage($entPage->getId(), $lang);
                if ($entPageLang instanceof \CMS\Bundle\AdminBundle\Entity\PageLanguage) {
                    $theArticle->setPageLanguage($entPageLang);
                }
            }
        }

        return $this->render('CMSAdminBundle:Article:index.html.twig', array(
            'entities' => $entities,
            'lastPage' => $lastPage,
            'previousPage' => $previousPage,
            'currentPage' => $page,
            'nextPage' => $nextPage,
            'total' => $total,
            'lang' => intval($lang),
            'langList' => $langList,
        ));
    }

    /**
     * @return type
     */
    public function newAction()
    {
        $entity = new Article();
        //get list language
        $repLanguage = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:Language");
        //Get list language
        $langList = $repLanguage->getList();

        if (is_array($langList)) {
            foreach ($langList as $language) {
                $articleLanguage = new ArticleLanguage();
                $articleLanguage->setLanguage($language);
                $articleLanguage->setArticle($entity);

                $entity->addArticleLanguage($articleLanguage);

                if ($language->getIsDefault()) {
                    $defaultLanguage = $language;
                }
            }
        }

        $form = $this->createForm(new ArticleType(), $entity);

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getEntityManager();
                $entityManager->persist($entity);
                foreach ($entity->getArticleLanguages() as $articleLanguage) {
                    $plTitle = $articleLanguage->getTitle();
                    if (empty($plTitle)) {
                        $entity->removeArticleLanguage($articleLanguage);
                        $entityManager->remove($articleLanguage);
                    }
                }

                $entityManager->flush();

                //update alias for each $articleLanguage
                $this->getDoctrine()->getRepository("CMSAdminBundle:ArticleLanguage")
                    ->updateAlias($entity);

                $this->getRequest()->getSession()->getFlashBag()->add('cms_flash_success', 'Page insert successfull!');

                return $this->redirect(
                    $this->generateUrl('admin_article')
                );
            } else {
                $this->getRequest()->getSession()->getFlashBag()->add('cms_flash_error', 'Form invalid');
            }
        }

        //get Medias
        $optMedias = $this->getDoctrine()->getRepository("CMSAdminBundle:Media")
            ->findAll();

        return $this->render('CMSAdminBundle:Article:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'langList' =>$langList,
            'optMedias' => $optMedias,
            'selectedMedias' => array(),
            'mediaPath' => $this->container->getParameter('upload'),
            'defaultLanguage' => $defaultLanguage
        ));

    }

    /**
     * @param type $id
     *
     * @return type
     */
    public function editAction($id)
    {
        $entity = $this->getDoctrine()->getRepository("CMSAdminBundle:Article")
            ->find($id);

        if (!$entity) {
            //go to page index with error
            $this->getRequest()->getSession()->getFlashBag()
                ->add('cms_flash_error', 'Could not find page with id ' . $id);

            return $this->redirect($this->generateUrl('admin_article'));
        }

        //get list language
        $langList = $this->getDoctrine()
            ->getRepository("CMSAdminBundle:Language")
            ->findAll();

        if (is_array($langList)) {
            foreach ($langList as $language) {
                if (!$entity->hasLanguage($language)) {
                    $articleLanguage = new ArticleLanguage();
                    $articleLanguage->setLanguage($language);
                    $articleLanguage->setArticle($entity);
                    $entity->addArticleLanguage($articleLanguage);
                }
                if ($language->getIsDefault()) {
                    $defaultLanguage = $language;
                }
            }
        }

        $articleType = new ArticleType();
        $form = $this->createForm($articleType, $entity);

        if ($this->getRequest()->iCMSethod('POST')) {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getEntityManager();
                foreach ($entity->getArticleLanguages() as $articleLanguage) {
                    $plTitle = $articleLanguage->getTitle();
                    if (empty($plTitle)) {
                        $entity->removeArticleLanguage($articleLanguage);
                        $entityManager->remove($articleLanguage);
                    }
                }
                $entityManager->persist($entity);

                $entityManager->flush();

                //update alias for each $articleLanguage
                $this->getDoctrine()->getRepository("CMSAdminBundle:ArticleLanguage")
                    ->updateAlias($entity);

                $this->getRequest()->getSession()->getFlashBag()->add('cms_flash_success', 'Page edit successfull!');

                return $this->redirect(
                    $this->generateUrl('admin_article')
                );
            } else {
                $this->getRequest()->getSession()->getFlashBag()->add('cms_flash_error', 'Form invalid');
            }
        }

        //get Medias
        $optMedias = $this->getDoctrine()->getRepository("CMSAdminBundle:Media")
            ->findAll();

        return $this->render('CMSAdminBundle:Article:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'langList' =>$langList,
            'defaultLanguage' => $defaultLanguage,
            'optMedias' => $optMedias,
            'mediaPath' => $this->container->getParameter('upload'),
        ));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request request
     * @param int                                       $id      the id
     *
     * @return type
     */
    public function deleteAction(Request $request, $id)
    {
        $rep = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:ArticleLanguage");

        $rst = $rep->deleteByIds(array($id));

        // set referrer redirect
        $referrer = $this->getRequest()->server->get('HTTP_REFERER');

        if (!$referrer) {
            return $this->redirect(
                $this->generateUrl('admin_article')
            );
        } else {
            return $this->redirect($referrer);
        }
    }

}