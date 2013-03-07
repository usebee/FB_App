<?php

namespace CMS\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleLanguage
 *
 * @ORM\Table(name="cms_article_language")
 * @ORM\Entity(repositoryClass="CMS\Bundle\AdminBundle\Repository\ArticleLanguageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ArticleLanguage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    private $alias;

    /**
     * @ORM\ManyToOne(targetEntity="Language")
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="article_languages", cascade={"persist"})
     */
    private $article;

    /**
     *
     * @var PageLanguage
     */
    private $_pageLang;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ArticleLanguage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ArticleLanguage
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return ArticleLanguage
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return ArticleLanguage
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set language
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Language $language
     * @return ArticleLanguage
     */
    public function setLanguage(\CMS\Bundle\AdminBundle\Entity\Language $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \CMS\Bundle\AdminBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set article
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Article $article
     * @return ArticleLanguage
     */
    public function setArticle(\CMS\Bundle\AdminBundle\Entity\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \CMS\Bundle\AdminBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     *
     * @return type
     */
    public function getPageLang()
    {
        return $this->_pageLang;
    }

    /**
     *
     * @param \CMS\Bundle\AdminBundle\Entity\PageLanguage $pageLang
     */
    public function setPageLang(\CMS\Bundle\AdminBundle\Entity\PageLanguage $pageLang)
    {
        $this->_pageLang = $pageLang;
    }
}