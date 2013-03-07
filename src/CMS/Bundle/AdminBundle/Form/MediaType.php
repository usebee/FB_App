<?php

namespace CMS\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Media type form
 */
class MediaType extends AbstractType
{
    private $_option;

    /**
     * constructor
     *
     * @param type $options the option
     */
    public function __construct($options = array())
    {
        $this->_option = $options;
    }

    /**
     * build form
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder builder
     * @param array                                        $options options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($this->_option['requiredFile'])) {
            //Show form edit
            $builder->add('file', 'file', array('required' => false));
        } else {
            $builder->add('file', 'file', array('required' => true));
        }

        $builder->add('name')
            ->add('width')
            ->add('height')
            ->add('active')
            ->add('category');
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CMS\Bundle\AdminBundle\Entity\Media'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cms_bundle_adminbundle_mediatype';
    }

}
