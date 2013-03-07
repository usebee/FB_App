<?php

namespace CMS\Bundle\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Function for menu
 */
class MenuController extends Controller
{
    /**
     * Get menu with $parentId
     *
     * @param type $parentId the parent id
     *
     * @return type
     */
    public function getMenu($parentId = null)
    {
        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getEntityManager();

        // Get default language with session
        $session = $this->get('session');
        $langDefault = $session->get('_locale');
        if (is_null($langDefault)) {
            // get default language
            $options = $this->getDoctrine()
                            ->getRepository("CMSAdminBundle:Language")
                            ->getOptions();
            foreach ($options as $lang) {
                if ($lang['is_default']) {
                    $langDefault = $lang['key'];
                    break;
                }
            }
            if (is_null($langDefault)) {
                $langDefault = $this->getRequest()->getLocale();
            }
        }
        $repositoryLang = $entityManager->getRepository("CMSAdminBundle:Language");
        $lang = $repositoryLang->findOneBy(array('lang_key' => $langDefault));
        $langId = !empty($lang) ? $lang->getId() : null;

        $repositoryPage = $entityManager->getRepository("CMSAdminBundle:Page");

        // Get Main Page
        $mainPageParent = $repositoryPage->findOneBy(array('parent' => null));
        $mainPage = $repositoryPage->findBy(
            array(
                'is_home' => 0,
                'active' => 1,
                'parent' => $mainPageParent->getId()
            ),
            array('lft' => 'ASC')
        );
        $mainPageLang = array();
        foreach ($mainPage as $page) {
            $arrPageLang = $page->getPageLanguages()->toArray();
            foreach ($arrPageLang as $pageLang) {
                $mainPageLang[] = $this->checkEntityLanguage($pageLang, $langId);
            }
        }
        // Get child Page
        $childPageParent = array();
        $childPageLang = array();
        $listArtilceOfParent = array();
        if (!is_null($parentId)) {
            // Get list article of this $parentId
            $repositoryArticle = $entityManager->getRepository("CMSAdminBundle:Article");
            $articleOfParent = $repositoryArticle->findBy(array(
                'page' => $parentId,
                'active' => 1
            ));
            $listArtilceOfParent = array();
            if (count($articleOfParent) > 0) {
                foreach ($articleOfParent as $article) {
                    $arrArticleLang = $article->getArticleLanguages()->toArray();
                    foreach ($arrArticleLang as $articleLang) {
                        $listArtilceOfParent[] = $this->checkEntityLanguage($articleLang, $langId);
                    }
                }
            }
            // Get list child page language of $parentId
            $childPageParent = $repositoryPage->findOneBy(array('id' => $parentId, 'active' => 1));
            $childPages = $repositoryPage->findBy(
                array(
                    'parent' => $parentId,
                    'active' => 1
                ),
                array('lft' => 'ASC')
            );
            $childPageLang = array();
            foreach ($childPages as $page) {
                $arrChildPageLang = $page->getPageLanguages()->toArray();
                foreach ($arrChildPageLang as $pageLang) {
                    $childPageLang[] = $this->checkEntityLanguage($pageLang, $langId);
                }
            }
        }

        return array(
            'mainPage' => array_filter($mainPageLang),
            'mainCategory' => $mainPageParent,
            'childPage' => array_filter($childPageLang),
            'childCategory' => $childPageParent,
            'listArtilceOfParent' => array_filter($listArtilceOfParent),
            'langId' => $lang->getId()
        );
    }

    private function checkEntityLanguage($entity, $langId)
    {
        if ($entity->getLanguage()->getId() == $langId) {
            return $entity;
        } else {
            return array();
        }
    }

    /**
     * Get Main Menu of site
     *
     * @param type $content the content
     *
     * @return content
     */
    public function getMainMenu($content)
    {
        return $this->render(
            'CMSFrontBundle:Menu:menu.html.twig',
            array('pages' => $content['mainPage'])
        );
    }

    /**
     * Get child menu of menu content
     *
     * @param type $content Content of GetMenu
     * @param type $slug    slug
     *
     * @return content
     */
    public function getChildMenu($content, $slug)
    {
        return $this->render(
            'CMSFrontBundle:Menu:child_menu.html.twig',
            array(
                'lang' => $content['langId'],
                'slug' => json_decode($slug, true),
                'category' => $content['childCategory'],
                'pages' => $content['childPage'],
                'listArtilceOfParent' => $content['listArtilceOfParent']
            )
        );
    }
}
