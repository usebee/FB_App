<?php

namespace CMS\Bundle\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CMS\Bundle\AdminBundle\Entity\Page;

/**
 * Load tree page data
 */
class LoadTreeData implements FixtureInterface
{

    /**
     * Function to load data
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $entityPage = new Page();
        $entityPage->setLft(0);
        $entityPage->setRgt(0);
        $manager->persist($entityPage);
//        $manager->flush();
    }

}