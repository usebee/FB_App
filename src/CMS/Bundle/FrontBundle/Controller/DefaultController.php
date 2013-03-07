<?php

namespace CMS\Bundle\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use CMS\Bundle\FrontBundle\Common;
use CMS\Bundle\FrontBundle\Controller\MenuController;

/**
 * default controller
 */
class DefaultController extends Controller
{

    /**
     * Default Action
     *
     * @param type $slug
     *
     * @return type
     */
    public function indexAction($slug = null)
    {
        // Get language
        $arrLang = $this->getCurrentLanguage();
        $lang = $arrLang['locale'];
        $language = $this->getDoctrine()->getRepository('CMSAdminBundle:Language')
                ->findOneBy(array('lang_key' => $lang));
        $langId = $language->getId();

        // Set default params
        $typeTemplate = 1;
        $seoSlug = Common::getSeoSlug($slug);
        $mediaPath = $this->container->getParameter('upload');

        // Get list news in home
        $homePage = $this->getPageHomeLang($langId);
        if (!empty($homePage['media'])) {
            $media = $homePage['media'];
        }
        $listHomeArticle = $homePage['listHomeArticle'];
        $listHomePage = $homePage['listPageHome'];

        $arrJsonImgHome = array();
        if ($listHomePage != null) {
            foreach ($listHomePage as $value) {
                $mediaHome = $value->getPage()->getMediaId()->toArray();
                if (isset($mediaHome[0])) {
                    $arrJsonImgHome[] = $this->getRequest()
                                            ->getUriForPath($mediaPath . $mediaHome[0]->getName());
                }
            }
        }

        // Get main Page
        $pageLang = $this->getFristPage($seoSlug[0], $langId);
        if ($pageLang !== null) {
            $arrayMedia = $pageLang->getPage()->getMediaId()->toArray();
            if (count($arrayMedia) > 0) {
                $media = $arrayMedia[0];
            }
            // Get type templates
            // Currently only the first level is to change the layout.
            $typeTemplate = $pageLang->getPage()->getType();
            if (empty($typeTemplate)) {
                $typeTemplate = 1;
            }
        }

        // Set default varliable
        $listFirstPage = $this->getListArticleFirstPage($pageLang, $langId);
        $firstPageLang = $listFirstPage['firstPageLang'];
        $listArticleLang = $listFirstPage['listArticleLang'];
        $pageLevelTwo = $this->getPageLevelTwo($seoSlug, $langId);
        $listFirstPageArticle = $pageLevelTwo['listArticleLang'];
        $listPageLevelTwo = (count($seoSlug) > 1) ?
                $pageLevelTwo['listChildPageLang'] : $firstPageLang;

        if (isset($listPageLevelTwo[0])) {
            $pageLevelTwoId = $listPageLevelTwo[0]->getPage()->getId();
        } else {
            $pageLevelTwoId = null;
        }

        $listArticleThree = $this->getFirstArticleLevelThree($pageLevelTwoId, $langId);

        $menu = new MenuController();
        $menu->setContainer($this->container);
        $parentId = isset($pageLang) ? $pageLang->getPage()->getId() : null;
        $menuContent = $menu->getMenu($parentId);


        //Get is_showreel page
        $pageIsShowreel = $this->getIsShowreelPageLanguage($language);

        // Load multi layout with $typeTemplate
        return $this->render(
            "CMSFrontBundle:layouts:template_" . $typeTemplate . ".html.twig", array(
            'slug' => $seoSlug,
            'langId' => $langId,
            'slugParams' => json_encode($seoSlug),
            'stringUrl' => $slug,
            'menuContent' => $menuContent,
            'mainMenu' => $menu->getMainMenu($menuContent),
            'childMenu' => $menu->getChildMenu($menuContent, json_encode($seoSlug)),
            'mainPage' => $pageLang,
            'media' => isset($media) ? $media : null,
            'mediaPath' => $mediaPath,
            'articles' => $listArticleLang,
            'listFirstPageArticle' => $listFirstPageArticle,
            'listHomeArticle' => $listHomeArticle,
            'listHomePage' => $listHomePage,
            'arrHomeImg' => json_encode($arrJsonImgHome),
            'listPageLevelTwo' => $listPageLevelTwo,
            'listArticleThree' => $listArticleThree,
            'pageIsShowreel' => $pageIsShowreel,
        ));
    }

    /**
     * change language
     *
     * @param type $lang Language key
     *
     * @return type
     */
    public function changeLanguageAction($lang)
    {
        $request = $this->getRequest();
        $oldLang = $request->getLocale();

        $session = $this->get('session');
        $locale = strtolower($lang);
        $session->set('_locale', $locale);
        $request->setLocale($locale);

        if ($oldLang != $locale) {
            $router = $this->get('router');
            // Create URL path to pass it to matcher
            $referer = $request->headers->get('referer');
            $urlParts = parse_url($referer);
            $basePath = $request->getBaseUrl();
            $path = str_replace($basePath, '', $urlParts['path']);
            $newPath = $this->changeRouter($path, $router, $locale);

            return $this->redirect($newPath);
        } else {
            $referer = $request->headers->get('referer');

            return $this->redirect($referer);
        }
    }

    /**
     * Get current language
     *
     * @return array $options, $locale of language
     */
    public function getlanguageAction()
    {
        $arrLang = $this->getCurrentLanguage();

        return $this->render('CMSFrontBundle:Default:getlanguage.html.twig', array(
                    'options' => $arrLang['listLang'],
                    'curLang' => $arrLang['locale']
                ));
    }

    private function changeRouter($oldPath, $oldRoute, $newLang)
    {
        $path = '';
        // Match route and get it's arguments
        $route = $oldRoute->match($oldPath);

        $routeName = $route['_route'];
        $params = explode('/', $oldPath);

        $newSlug = '';
        for ($i = 1; $i < count($params); $i++) {
            //if the last slug for article
            if (substr($params[$i], -1, 5) == '.html') {
                $newSlug .= $this->getDoctrine()
                                ->getRepository("CMSAdminBundle:ArticleLanguage")
                                ->getSwitchSlug($params[$i], $newLang) . '.html';
                break;
            } else if ($params[$i] != '') {
                $newSlug .= $this->getDoctrine()
                                ->getRepository("CMSAdminBundle:PageLanguage")
                                ->getSwitchSlug($params[$i], $newLang) . '/';
            }
        }
        //cut the last /
        $slug = rtrim($newSlug, '/');
        if ($newSlug != '') {
            $path = $this->generateUrl($routeName, array('slug' => $slug));
        } else {
            $path = $this->generateUrl('cms_front');
        }

        return $path;
    }

    /**
     * Get first page form slug follow language
     *
     * @param string  $slug   Alias of category
     * @param integer $langId Language Id
     *
     * @return $page entity
     */
    public function getFristPage($slug, $langId)
    {
        if ($slug === null) {
            return null;
        }

        $doctrine = $this->getDoctrine();

        // Get id from slug
        $entityManager = $doctrine->getRepository('CMSAdminBundle:PageLanguage');
        $page = $entityManager->findOneBy(array(
            'alias' => $slug,
            'language' => $langId
        ));

        return $page;
    }

    /**
     * Get list article language with slug
     *
     * @param string  $slug   alias of url
     * @param integer $langId language Id
     *
     * @return object $listArticleLang Entity Of CMSAdminBundle:ArticleLanguage
     */
    public function getPageLevelTwo($slug, $langId = null)
    {
        $doctrine = $this->getDoctrine();
        if (!isset($slug[1])) {
            return null;
        } else {
            $slug2 = $slug[1];
        }

        // Get Page language from $slug2
        $pageTwoLang = $doctrine->getRepository('CMSAdminBundle:PageLanguage')
            ->findOneBy(array(
                'alias' => $slug2,
                'language' => $langId
            ));

        // Get Article from $pageTwoLang
        if (!empty($pageTwoLang)) {
            $pageId = $pageTwoLang->getPage()->getId();
        } else {
            $pageId = null;
        }
        $listArticle = $doctrine->getRepository('CMSAdminBundle:Article')
                ->findBy(array('page' => $pageId, 'active' => 1));

        $listArticleLang = $this->getArticleLangFromListArticle($listArticle, $langId);

        // Get childrend of page if exist
        $childPageLang = $doctrine->getRepository('CMSAdminBundle:Page')
                                ->findBy(
                                    array(
                                        'parent' => $pageId,
                                        'active' => 1
                                    ), array('lft' => 'ASC')
                                );
        $listChildPageLang = array();
        foreach ($childPageLang as $child) {
            $arrChildLang = $child->getPageLanguages()->toArray();
            foreach ($arrChildLang as $childLang) {
                if ($childLang->getLanguage()->getId() == $langId) {
                    $listChildPageLang[] = $childLang;
                }
            }
        }

        return array(
            'listArticleLang' => $listArticleLang,
            'listChildPageLang' => $listChildPageLang
        );
    }

    /**
     * Get First Article From Page level two
     *
     * @param type $pageLevelTwoId Id Of Page level two
     * @param type $langId         Language Id
     *
     * @return array
     */
    public function getFirstArticleLevelThree($pageLevelTwoId, $langId)
    {
        $doctrine = $this->getDoctrine();

        if (!isset($pageLevelTwoId) || !isset($langId)) {
            return null;
        }
        // Get Page From Page level two
        $listArticle = $doctrine->getRepository('CMSAdminBundle:Article')
                                ->findBy(array(
                                    'page' => $pageLevelTwoId,
                                    'active' => 1
                                ));
        $listFristArticleLang = array();
        foreach ($listArticle as $article) {
            $articleLanguages = $article->getArticleLanguages()->toArray();
            foreach ($articleLanguages as $articleLanguage) {
                if ($articleLanguage->getLanguage()->getId() == $langId) {
                    $listFristArticleLang[] = $articleLanguage;
                }
            }
        }

        if (count($listFristArticleLang) > 0) {
            return $listFristArticleLang;
        } else {
            return null;
        }
    }

    /**
     * Get list article language from list article
     *
     * @param object  $listArticle Entity of CMSAdminBundle:Article
     * @param integer $langId      Language Id
     *
     * @return object $listArticleLang Entity of CMSAdminBundle:ArticleLanuage
     */
    public function getArticleLangFromListArticle($listArticle, $langId)
    {
        $listArticleLang = null;

        foreach ($listArticle as $article) {
            $arrArticleLang = $article->getArticleLanguages()->toArray();
            foreach ($arrArticleLang as $articleLang) {
                if ($articleLang->getLanguage()->getId() == $langId) {
                    $listArticleLang[] = $articleLang;
                }
            }
        }

        return $listArticleLang;
    }

    /**
     * Get content article of popup
     *
     * @param string $bookmark Position of bookmark in content
     * @param string $slug     Alias of Url
     * @param type   $lang     Language Id
     *
     * @return Content of popup
     */
    public function popupAction($bookmark = null, $slug = null, $lang = null)
    {
        $listArticleLang = array();

        if (!empty($slug)) {
            $seoSlug = json_decode($slug, true);
            if (is_null($seoSlug)) {
                if (!empty($bookmark) && $bookmark == 'listPage') {
                    // Get list Page of Slug
                    $pageSlug = $this->getDoctrine()
                        ->getRepository('CMSAdminBundle:PageLanguage')
                        ->findOneBy(array(
                            'alias' => $slug,
                            'language' => $lang
                        ));
                    $listArticleOfSlug = array();
                    if ($pageSlug) {
                        $articleOfSlug = $this->getDoctrine()
                                              ->getRepository('CMSAdminBundle:Article')
                                              ->findBy(array(
                                                  'page' => $pageSlug->getPage()->getId(),
                                                  'active' => 1
                                              ));
                        foreach ($articleOfSlug as $article) {
                            $arrArticleLang = $article->getArticleLanguages()->toArray();
                            foreach ($arrArticleLang as $articleLang) {
                                if ($articleLang->getLanguage()->getId() == $lang) {
                                    $listArticleOfSlug[] = $articleLang;
                                }
                            }
                        }
                    }

                    return $this->render('CMSFrontBundle:Default:popup_right.html.twig', array(
                        'pages' => $listArticleOfSlug,
                        'langId' => $lang
                    ));
                }

                // Get content of article with slug
                $listArticleLang = $this->getDoctrine()
                    ->getRepository('CMSAdminBundle:ArticleLanguage')
                    ->findBy(array(
                        'alias' => $slug,
                        'language' => $lang
                    ));

                return $this->render('CMSFrontBundle:Default:popup.html.twig', array(
                            'bookmark' => null,
                            'pages' => $listArticleLang
                        ));
            }
            if (count($seoSlug) == 1) {
                $pageLang = $this->getFristPage($seoSlug[0], $lang);
                $dataArticleLang = $this->getListArticleFirstPage($pageLang, $lang);
                $listArticleLang = $dataArticleLang['listArticleLang'];
            } else {
                $pageLevelTwo = $this->getPageLevelTwo($seoSlug, $lang);
                $listArticleLang = $pageLevelTwo['listArticleLang'];
            }
        }

        return $this->render('CMSFrontBundle:Default:popup.html.twig', array(
                    'bookmark' => $bookmark,
                    'pages' => $listArticleLang
                ));
    }

    /**
     * Get List Article of first page
     *
     * @param object $pageLang Object page language
     * @param type   $langId   Language Id
     *
     * @return type
     */
    public function getListArticleFirstPage($pageLang, $langId)
    {
        if (is_null($pageLang)) {
            return array(
                'listArticleLang' => null,
                'firstPageLang' => null
            );
        }

        $listArticleLang = array();
        $firstPageLang = array();
        // Get first category
        $firstPage = $this->getDoctrine()
            ->getRepository('CMSAdminBundle:Page')
            ->findOneBy(array(
                'parent' => $pageLang->getPage()->getId(),
                'lft' => $pageLang->getPage()->getLft() + 1
            ));
        if (!empty($firstPage)) {
            // Get list article form first page
            $listArticle = $this->getDoctrine()
                ->getRepository('CMSAdminBundle:Article')
                ->findBy(array(
                    'page' => $firstPage->getId(),
                ));
            $listArticleLang = $this->getArticleLangFromListArticle($listArticle, $langId);

            // Get list childrend page
            $listChildFirstPage = $this->getDoctrine()->getRepository('CMSAdminBundle:Page')
                                  ->findBy(array(
                                      'parent' => $firstPage->getId(),
                                      'active' => 1
                                  ), array('lft' => 'ASC'));

            foreach ($listChildFirstPage as $childPage) {
                $arrChildLang = $childPage->getPageLanguages()->toArray();
                foreach ($arrChildLang as $childLang) {
                    if ($childLang->getLanguage()->getId() == $langId) {
                        $firstPageLang[] = $childLang;
                    }
                }
            }
        } else {
            return array(
                'listArticleLang' => null,
                'firstPageLang' => null
            );
        }

        return array(
            'listArticleLang' => $listArticleLang,
            'firstPageLang' => $firstPageLang
        );
    }

    /**
     * Get object of home page
     *
     * @param type $languageId
     *
     * @return array List artilce of home page,
     *               media of home page
     *               list page of home page
     */
    public function getPageHomeLang($languageId)
    {
        // Get page home Language
        $pageHomeLang = $this->getDoctrine()
                ->getRepository('CMSAdminBundle:Page')
                ->findOneBy(array(
                    'is_home' => 1,
                    'active' => 1
                ), array('lft' => 'ASC'));
        $listHomeArticle = null;
        $listPages = null;
        $media = null;

        // Get Article and list Pages of Home Page
        if (is_object($pageHomeLang)) {
            // Get list page lang of home page
            $listChildPageHome = $this->getDoctrine()
                    ->getRepository('CMSAdminBundle:Page')
                    ->findBy(array(
                        'parent' => $pageHomeLang->getId(),
                        'active' => 1
                    ), array('lft' => 'ASC'));

            foreach ($listChildPageHome as $value) {
                $arrChildLang = $value->getPageLanguages()->toArray();
                foreach ($arrChildLang as $childLang) {
                    if ($childLang->getLanguage()->getId() == $languageId) {
                        $listPages[] = $childLang;
                    }
                }
            }
            $homeArticle = $this->getDoctrine()
                                ->getRepository('CMSAdminBundle:Article')
                                ->findBy(array(
                                    'page' => $pageHomeLang->getId(),
                                    'active' => 1
                                ));
            foreach ($homeArticle as $article) {
                $arrArticleLang = $article->getArtilceLanguages()->toArray();
                foreach ($arrArticleLang as $articleLang) {
                    if ($arrArticleLang->getLanguage()->getId() == $languageId) {
                        $listHomeArticle[] = $articleLang;
                    }
                }
            }

            $arrayMedia = $pageHomeLang->getMediaId()->toArray();
            if (count($arrayMedia) > 0) {
                $media = $arrayMedia[0];
            }
        }

        return array(
            'listPageHome' => $listPages,
            'listHomeArticle' => $listHomeArticle,
            'media' => $media
        );
    }

    /**
     * @param \CMS\Bundle\AdminBundle\Entity\Language $language language
     *
     * @return type
     */
    private function getIsShowreelPageLanguage(\CMS\Bundle\AdminBundle\Entity\Language $language)
    {
        $pageLanguages = $this->getDoctrine()->getRepository('CMSAdminBundle:PageLanguage')
                ->findShowreel($language);
        if (count($pageLanguages)>0) {
            return $pageLanguages[0];
        } else {
            return null;
        }
    }

    /**
     * Show slide show for article page
     *
     * @param string  $slug   Alias of article to get slide show
     * @param integer $langId Language Id
     *
     * @return type
     */
    public function slideAction($slug = null, $langId = null)
    {
        if (is_null($slug)) {
            return null;
        }
        // Get Article form $slug
        $article = $this->getDoctrine()->getEntityManager()
                        ->createQueryBuilder()
                        ->select('al')
                        ->from('CMSAdminBundle:ArticleLanguage', 'al')
                        ->join('al.article', 'a')
                        ->where('al.alias =:alias')
                        ->setParameter('alias', $slug)
                        ->andWhere('a.active = 1')
                        ->andWhere('al.language = :langId')
                        ->setParameter('langId', $langId)
                        ->getQuery()->getResult();

        if (isset($article[0])) {
            $listMedia = $article[0]->getArticle()->getMediaId()->toArray();

            $listHtmlContent = $this->render('CMSFrontBundle:Default:slide.html.twig');
            $mediaPath = $this->container->getParameter('upload');
            foreach ($listMedia as $media) {
                $listGallery[] = $this->getRequest()->getUriForPath($mediaPath . $media->getName());
            }
            $arrContent = array(
                'htmlContent' => $listHtmlContent->getContent(),
                'listGallery' => $listGallery,
            );
            echo json_encode($arrContent);die;
        } else {
            $listMedia = null;
        }

        return $this->render('CMSFrontBundle:Default:slide.html.twig', array(
            'medias' => $listMedia
        ));
    }

    /**
     * Get current language with locale and array list language
     *
     * @return array
     */
    public function getCurrentLanguage()
    {
        $session = $this->get('session');
        $locale = $session->get('_locale');
        $listLang = $this->getDoctrine()
                ->getRepository("CMSAdminBundle:Language")
                ->getOptions();
        if (is_null($locale)) {
            // get default language
            foreach ($listLang as $lang) {
                if ($lang['is_default']) {
                    $locale = $lang['key'];
                    break;
                }
            }
            // if no have default language
            if (is_null($locale)) {
                $locale = $this->getRequest()->getLocale();
            }
        }

        return array(
            'locale' => $locale,
            'listLang' => $listLang
        );
    }
}