<?php

namespace CMS\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="cms_page_language")
 * @ORM\Entity(repositoryClass="CMS\Bundle\AdminBundle\Repository\PageLanguageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PageLanguage
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
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="page_languages", cascade={"persist"})
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity="Language")
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    public function __toString()
    {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     * @return PageLanguage
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
     * Set name
     *
     * @param string $alias
     * @return PageLanguage
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }


    /**
     * Set description
     *
     * @param string $description
     * @return PageLanguage
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
     * Set page
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Page $page
     * @return PageLanguage
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
     * Set language
     *
     * @param \CMS\Bundle\AdminBundle\Entity\Language $language
     * @return PageLanguage
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
     * Get the name with tree level
     * 
     * @return string
     */
    public function getTreeName()
    {
        //the space is chr(255)
        $treeName = str_repeat('    ', $this->getPage()->getLevel() - 1) .
                            ($this->getPage()->getLevel() == 1 ? '' : '└') .
                            ' ' .
                            $this->getName();
        return $treeName;
    }


}