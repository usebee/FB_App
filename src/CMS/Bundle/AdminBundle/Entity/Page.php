<?php

namespace CMS\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="cms_page")
 * @ORM\Entity(repositoryClass="CMS\Bundle\AdminBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Page
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PageLanguage", mappedBy="page", cascade={"all"})
     */
    protected $page_languages;

    /**
     * @var integer
     *
     * @ORM\Column(name="lft", type="integer", nullable=true)
     */
    private $lft;

    /**
     * @var integer
     *
     * @ORM\Column(name="rgt", type="integer", nullable=true)
     */
    private $rgt;

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
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="Media")
     * @ORM\JoinTable(name="cms_page_media")
     */
    private $media_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_home", type="boolean", nullable=true)
     */
    private $is_home = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_showreel", type="boolean", nullable=true)
     */
    private $is_showreel = false;

    /**
     *
     * @var Language
     */
    private $language;


    public function __toString()
    {
        $pageLanguages = $this->page_languages->toArray();
        if (is_array($pageLanguages)) {
            if (isset($pageLanguages[0])) {
//                var_dump($pageLanguages[0]);
                return $pageLanguages[0]->getTreeName();
            }
        }

        return '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->media_id = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->page_languages = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set lft
     *
     * @param integer $lft
     * @return Page
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return Page
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Page
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
     * @return Page
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
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updated_at = new \DateTime();
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Page
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
     * Set parent
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Page $parent
     * @return Page
     */
    public function setParent(\CMS\Bundle\AdminBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CMS\Bundle\AdminBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Page $children
     * @return Page
     */
    public function addChildren(\CMS\Bundle\AdminBundle\Entity\Page $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Page $children
     */
    public function removeChildren(\CMS\Bundle\AdminBundle\Entity\Page $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add page_languages
     *
     * @param \CMS\Bundle\AdminBundle\Entity\PageLanguage $pageLanguages
     * @return Page
     */
    public function addPageLanguage(\CMS\Bundle\AdminBundle\Entity\PageLanguage $pageLanguages)
    {
        $this->page_languages[] = $pageLanguages;

        return $this;
    }

    /**
     * Remove page_languages
     *
     * @param \CMS\Bundle\AdminBundle\Entity\PageLanguage $pageLanguages
     */
    public function removePageLanguage(\CMS\Bundle\AdminBundle\Entity\PageLanguage $pageLanguages)
    {
        $this->page_languages->removeElement($pageLanguages);
    }

    /**
     * Get page_languages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPageLanguages()
    {
        return $this->page_languages;
    }

    /**
     * Set user
     *
     * @param \CMS\Bundle\AdminBundle\Entity\User $user
     * @return Page
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
     * Add media_id
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Media $mediaId
     * @return Page
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

    public function getLevel()
    {
        if (null === $this->parent) {
            return 0;
        } else {
            return $this->parent->getLevel() + 1;
        }
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Page
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set is_home
     *
     * @param boolean $isHome
     * @return Page
     */
    public function setIsHome($isHome)
    {
        $this->is_home = $isHome;

        return $this;
    }

    /**
     * Get is_home
     *
     * @return boolean
     */
    public function getIsHome()
    {
        return $this->is_home;
    }

    /**
     * Set is_showreel
     *
     * @param boolean $isShowreel
     * @return Page
     */
    public function setIsShowreel($isShowreel)
    {
        $this->is_showreel = $isShowreel;

        return $this;
    }

    /**
     * Get is_showreel
     *
     * @return boolean
     */
    public function getIsShowreel()
    {
        return $this->is_showreel;
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

    public function getCurrentPageLanguage()
    {
        $pageLanguages = $this->page_languages->toArray();
        if (is_array($pageLanguages)) {
            if (null !== $this->language) {
                foreach ($pageLanguages as $pageLanguage) {
                    if ($pageLanguage->getLanguage()->getId() == $this->language->getId()) {
                        return $pageLanguage;
                    }
                }
            }
        }

        return null;
    }

    public function hasLanguage(Language $language)
    {
        $result = false;
        if (count($this->page_languages->toArray()) > 0) {
            foreach ($this->page_languages as $plTemp) {
                if ($language->getId() == $plTemp->getLanguage()->getId()) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

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
}