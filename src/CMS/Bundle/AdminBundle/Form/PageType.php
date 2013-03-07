<?php

namespace CMS\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CMS\Bundle\AdminBundle\Repository\PageRepository;

/**
 * page type controller
 */
class PageType extends AbstractType
{
    private $templates;

    /**
     * Construct
     *
     * @param type $templates
     */
    public function __construct($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder Builder
     * @param array                                        $options Options Form
     *
     * @return type
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', 'entity', array(
                'required' => true,
                'class' => 'CMSAdminBundle:Page',
                'query_builder' => function (PageRepository $pRe) {
                    return $pRe->createQueryBuilder('p')
                        ->orderBy('p.lft', 'ASC');
                }
            ))
            ->add('active')
            ->add('media_id', null, array('required' => false))
            ->add('type', 'choice', array(
                'choices' => $this->templates,
                'required' => true
            ))
            ->add('is_home')
            ->add('is_showreel')
            ->add('page_languages', 'collection', array(
                'type' => new PageLanguageType()
            ));
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\Bundle\AdminBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cms_bundle_adminbundle_pagetype';
    }
}
