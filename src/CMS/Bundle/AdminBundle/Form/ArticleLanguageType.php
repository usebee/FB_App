<?php

namespace CMS\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * page type controller
 */
class ArticleLanguageType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder builder
     * @param array                                        $options options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('required' => false))
            ->add('content', 'textarea', array(
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'medium',
                ),
                'required' => false,
            ))
            ->add('description', 'textarea', array(
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'medium',
                ),
                'required' => false,
            ));
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\Bundle\AdminBundle\Entity\ArticleLanguage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cms_bundle_adminbundle_articlelanguagetype';
    }
}
