<?php

namespace CMS\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuLanguage
 *
 * @ORM\Table(name="cms_menu_language")
 * @ORM\Entity(repositoryClass="CMS\Bundle\AdminBundle\Repository\MenuLanguageRepository")
 * 
 */
class MenuLanguage
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Language")
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="Menu")
     */
    private $menu;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;


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
     * Set name
     *
     * @param string $name
     * @return MenuLanguage
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return MenuLanguage
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return MenuLanguage
     */
    public function setLink($link)
    {
        $this->link = $link;
    
        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set language
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Language $language
     * @return MenuLanguage
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
     * Set menu
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Menu $menu
     * @return MenuLanguage
     */
    public function setMenu(\CMS\Bundle\AdminBundle\Entity\Menu $menu = null)
    {
        $this->menu = $menu;
    
        return $this;
    }

    /**
     * Get menu
     *
     * @return \CMS\Bundle\AdminBundle\Entity\Menu 
     */
    public function getMenu()
    {
        return $this->menu;
    }
}