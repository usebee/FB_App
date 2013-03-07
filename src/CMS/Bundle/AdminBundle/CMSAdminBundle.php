<?php

namespace CMS\Bundle\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * CMSAdminBundle
 */
class CMSAdminBundle extends Bundle
{

    private static $containerInstance = null;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        self::$containerInstance = $container;
    }

    /**
     * @return type
     */
    public static function getContainer()
    {
        return self::$containerInstance;
    }

}
