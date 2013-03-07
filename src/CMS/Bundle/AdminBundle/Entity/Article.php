<?php

namespace CMS\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table(name="cms_article")
 * @ORM\Entity(repositoryClass="CMS\Bundle\AdminBundle\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Article
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
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Page")
     */
    private $page;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ArticleLanguage", mappedBy="article", cascade={"all"})
     */
    protected $article_languages;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\ManyToMany(targetEntity="Media", cascade={"persist"})
     * @ORM\JoinTable(name="cms_article_media")
     */
    private $media_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $edit_by_user;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var PageLanguage
     */
    private $page_language;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if (!$this->getCreatedAt()) {
            $this->created_at = new \DateTime();
            $this->updated_at = new \DateTime();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updated_at = new \DateTime();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->media_id = new \Doctrine\Common\Collections\ArrayCollection();
        $this->article_languages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->language = null;
    }

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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Article
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Article
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set deleted_at
     *
     * @param \DateTime $deletedAt
     * @return Article
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Get deleted_at
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Article
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set user
     *
     * @param \CMS\Bundle\AdminBundle\Entity\User $user
     * @return Article
     */
    public function setUser(\CMS\Bundle\AdminBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CMS\Bundle\AdminBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set page
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Page $page
     * @return Article
     */
    public function setPage(\CMS\Bundle\AdminBundle\Entity\Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \CMS\Bundle\AdminBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set page
     *
     * @param \CMS\Bundle\AdminBundle\Entity\PageLanguage $pageLanguage
     * @return Article
     */
    public function setPageLanguage(\CMS\Bundle\AdminBundle\Entity\PageLanguage $pageLanguage = null)
    {
        $this->page_language = $pageLanguage;

        return $this;
    }

    /**
     * Get page
     *
     * @return \CMS\Bundle\AdminBundle\Entity\Page
     */
    public function getPageLanguage()
    {
        return $this->page_language;
    }


    /**
     * Add article_languages
     *
     * @param \CMS\Bundle\AdminBundle\Entity\ArticleLanguage $articleLanguages
     * @return Article
     */
    public function addArticleLanguage(\CMS\Bundle\AdminBundle\Entity\ArticleLanguage $articleLanguages)
    {
        $this->article_languages[] = $articleLanguages;

        return $this;
    }

    /**
     * Remove article_languages
     *
     * @param \CMS\Bundle\AdminBundle\Entity\ArticleLanguage $articleLanguages
     */
    public function removeArticleLanguage(\CMS\Bundle\AdminBundle\Entity\ArticleLanguage $articleLanguages)
    {
        $this->article_languages->removeElement($articleLanguages);
    }

    /**
     * Get article_languages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticleLanguages()
    {
        return $this->article_languages;
    }

    /**
     * Add media_id
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Media $mediaId
     * @return Article
     */
    public function addMediaId(\CMS\Bundle\AdminBundle\Entity\Media $mediaId)
    {
        $this->media_id[] = $mediaId;

        return $this;
    }

    /**
     * Remove media_id
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Media $mediaId
     */
    public function removeMediaId(\CMS\Bundle\AdminBundle\Entity\Media $mediaId)
    {
        $this->media_id->removeElement($mediaId);
    }

    /**
     * Get media_id
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMediaId()
    {
        return $this->media_id;
    }

    /**
     * Set edit_by_user
     *
     * @param \CMS\Bundle\AdminBundle\Entity\User $editByUser
     * @return Article
     */
    public function setEditByUser(\CMS\Bundle\AdminBundle\Entity\User $editByUser = null)
    {
        $this->edit_by_user = $editByUser;

        return $this;
    }

    /**
     * Get edit_by_user
     *
     * @return \CMS\Bundle\AdminBundle\Entity\User
     */
    public function getEditByUser()
    {
        return $this->edit_by_user;
    }

     /**
     * Set Language
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Language $language
     */
    public function setLanguage(Language $language)
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

    public function getCurrentArticleLanguage()
    {
        $articleLanguages = $this->article_languages->toArray();
        if (is_array($articleLanguages)) {
            if (null !== $this->language) {
                foreach ($articleLanguages as $articleLanguage) {
                    if ($articleLanguage->getLanguage()->getId() == $this->language->getId()) {
                        return $articleLanguage;
                    }
                }
            }
        }

        return null;
    }

    public function hasLanguage(Language $language)
    {
        $result = false;
        if (count($this->article_languages->toArray()) > 0) {
            foreach ($this->article_languages as $plTemp) {
                if ($language->getId() == $plTemp->getLanguage()->getId()) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }
}