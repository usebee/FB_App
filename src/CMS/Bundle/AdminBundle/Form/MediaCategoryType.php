<?php

namespace CMS\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * media category type
 */
class MediaCategoryType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder builder
     * @param array                                        $options options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\Bundle\AdminBundle\Entity\MediaCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cms_bundle_adminbundle_mediacategorytype';
    }
}
