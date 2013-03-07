<?php

namespace CMS\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * page type controller
 */
class PageLanguageType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder builder
     * @param array                                        $options options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('required' => false))
            ->add('description', 'textarea', array(
                'required' => false,
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'medium',
                ),
            ));
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\Bundle\AdminBundle\Entity\PageLanguage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cms_bundle_adminbundle_pagelanguagetype';
    }
}
