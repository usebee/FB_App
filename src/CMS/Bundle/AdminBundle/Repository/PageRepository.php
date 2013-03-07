<?php

namespace CMS\Bundle\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PageRepository extends EntityRepository
{
    /**
     * get total Page
     *
     * @return type
     */
    public function getTotal()
    {
        $entities = $this->findAll();
        $total = count($entities);

        $rootEntities = $this->findBy(array('parent'=>null));
        $total -= count($rootEntities);

        return $total;
    }

    /**
     * get list by language
     *
     * @param type $max    max
     * @param type $offset offset
     *
     * @return type
     */
    public function getList($max = null, $offset = null)
    {
        return $this->findBy(
            array(),
            array('lft' => 'ASC'),
            $max,
            $offset
        );
    }

    /**
     * Reset is home page
     */
    public function resetIsHome()
    {
        $isHomePages = $this->_em->getRepository($this->_entityName)
            ->findBy(array('is_home'=>true));
        if ($isHomePages) {
            foreach ($isHomePages as $page) {
                $page->setIsHome(false);
                $this->_em->persist($page);
            }
        }
    }

    /**
     * Reset is showreel
     */
    public function resetIsShowreel()
    {
        $isShowreelPages = $this->_em->getRepository($this->_entityName)
            ->findBy(array('is_showreel'=>true));
        if ($isShowreelPages) {
            foreach ($isShowreelPages as $page) {
                $page->setIsShowreel(false);
                $this->_em->persist($page);
            }
//            $this->_em->flush();
        }
    }

    /**
     * get page root
     *
     * @return Page the entity page
     */
    public function getPageRoot()
    {
        $queryBuilder = $this->createQueryBuilder('j');
        $queryBuilder->andWhere('j.parent is NULL');
        $query = $queryBuilder->getQuery();
        $rst = $query->getResult();
        if (!empty($rst[0])) {

            return $rst[0];
        } else {
            $stmt = $this->_em
                    ->getConnection()
                    ->prepare("INSERT INTO {$this->_class->getTableName()} (lft, rgt) VALUES (0, 0)");
            $stmt->execute();
            $root = $this->_em->getRepository($this->_entityName)
                    ->find($this->_em->getConnection()->lastInsertId());

            return $root;
        }

        return null;
    }


    /**
     * getChildren
     *
     * @param type $parentId the id
     *
     * @return array
     */
    public function getChildren($parentId)
    {
        return $this->getEntityManager()
                        ->getRepository('CMSAdminBundle:Page')
                        ->createQueryBuilder('c')
                        ->where('c.parent = :parentId')
                        ->andWhere('c.parent != c.id')
                        ->setParameter('parentId', $parentId)
                        ->orderBy('c.lft', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

    /**
     * getChildrenArray
     *
     * @param type $parentId the id
     *
     * @return array
     */
    public function getChildrenArray($parentId)
    {
        return $this->getEntityManager()
                        ->getRepository('CMSAdminBundle:Page')
                        ->createQueryBuilder('c')
                        ->select('c.id, c.lft, c.rgt')
                        ->where('c.parent = :parentId')
                        ->andWhere('c.parent != c.id')
                        ->setParameter('parentId', $parentId)
                        ->orderBy('c.lft', 'ASC')
                        ->getQuery()
                        ->getArrayResult();
    }

    /**
     * @param type $idPage The id of page
     * @param type $lft    Position of left
     * @param type $rgt    Position of right
     *
     * @return type
     */
    public function setLftRgt($idPage, $lft, $rgt)
    {
        return $this->getEntityManager()
                        ->getRepository('CMSAdminBundle:Page')
                        ->createQueryBuilder('c')
                        ->update()
                        ->set('c.lft', $lft)
                        ->set('c.rgt', $rgt)
                        ->where('c.id = :id')
                        ->setParameter('id', $idPage)
                        ->getQuery()
                        ->execute();
    }

    /**
     * @param type $idPage The id of page
     *
     * @return type
     */
    public function getEntityArray($idPage)
    {
        return $this->getEntityManager()
                        ->getRepository('CMSAdminBundle:Page')
                        ->createQueryBuilder('c')
                        ->select('c.id, c.lft, c.rgt')
                        ->where('c.id = :id')
                        ->setParameter('id', $idPage)
                        ->getQuery()
                        ->getArrayResult();
    }

    /**
     * @param type $options the option
     *
     * @return null
     */
    public function findOneArrayBy($options = array())
    {
        $queryBuilder = $this->getEntityManager()
                ->getRepository('CMSAdminBundle:Page')
                ->createQueryBuilder('c')
                ->select('c.id, c.lft, c.rgt');
        $num = 0;
        foreach ($options as $key => $option) {
            $queryBuilder->andWhere("c.$key = :ii$num")
                    ->setParameter("ii$num", $option);
        }
        $rerult = $queryBuilder->getQuery()->getArrayResult();
        if (is_array($rerult) and count($rerult) > 0) {

            return $rerult[0];
        }

        return null;
    }

    /**
     * @return boolean
     */
    public function rebuildLftRgt()
    {
        //get all parent tree
        $trees = $this->getEntityManager()
                ->getRepository('CMSAdminBundle:Page')
                ->createQueryBuilder('c')
                ->select('c.id, c.lft, c.rgt')
                ->where('c.parent IS NULL')
                ->getQuery()
                ->getArrayResult();
        $begin = 1;
        $end = 0;
        foreach ($trees as $tree) {
            $this->postOrderTraversal($tree, $begin, $end);
            $begin = $end + 1;
        }

        return true;
    }

    /**
     * @param type $tree  tree
     * @param type $begin begin
     * @param type &$end  end
     */
    public function postOrderTraversal($tree, $begin, &$end)
    {
        $repositor = $this->_em->getRepository('CMSAdminBundle:Page');
        //get $tree childrens
        $children = $this->_em->getRepository('CMSAdminBundle:Page')
                ->getChildrenArray($tree['id']);

        $tree['lft'] = $begin;
        $end = ++$begin;
        //Travesal the tree
        foreach ($children as $child) {
            $repositor->postOrderTraversal($child, $begin, $end);
            $begin = ++$end;
        }
        $tree['rgt'] = $end;
        $this->setLftRgt($tree['id'], $tree['lft'], $tree['rgt']);
    }

    /**
     * @param \CMS\Bundle\AdminBundle\Entity\Page $page the page
     */
    public function moveUp(\CMS\Bundle\AdminBundle\Entity\Page $page)
    {
        //get the area upper
        $entityManager = $this->_em;
        $pageArray = array(
            'id' => $page->getId(),
            'lft' => $page->getLft(),
            'rgt' => $page->getRgt(),
        );

        $repositor = $entityManager->getRepository('CMSAdminBundle:Page');
        $pageUpper = $repositor->findOneArrayBy(array('rgt' => ($pageArray['lft'] - 1)));

        if ($pageUpper) {
            $del1 = $pageArray['rgt'] - $pageArray['lft'];
            $del2 = $pageUpper['rgt'] - $pageUpper['lft'];

            //calculate new lft, rgt of 2 node and swap
            $pageArray['lft'] = $pageUpper['lft'];
            $pageArray['rgt'] = $pageArray['lft'] + $del1;
            $pageUpper['lft'] = $pageArray['rgt'] + 1;
            $pageUpper['rgt'] = $pageUpper['lft'] + $del2;
            $end = 0;
            //save new order
            $repositor->postOrderTraversal($pageUpper, $pageUpper['lft'], $end);
            $repositor->postOrderTraversal($pageArray, $pageArray['lft'], $end);
        }
    }

    /**
     * @param \CMS\Bundle\AdminBundle\Entity\Page $page the page
     */
    public function moveDown(\CMS\Bundle\AdminBundle\Entity\Page $page)
    {
        //get the area upper
        $entityManager = $this->_em;
        $pageArray = array(
            'id' => $page->getId(),
            'lft' => $page->getLft(),
            'rgt' => $page->getRgt(),
        );

        $repositor = $entityManager->getRepository('CMSAdminBundle:Page');
        $pageUnder = $repositor->findOneArrayBy(array('lft' => ($pageArray['rgt'] + 1)));
        if ($pageUnder) {
            $del1 = $pageArray['rgt'] - $pageArray['lft'];
            $del2 = $pageUnder['rgt'] - $pageUnder['lft'];

            //calculate new lft, rgt of 2 node and swap
            $pageUnder['lft'] = $pageArray['lft'];
            $pageUnder['rgt'] = $pageArray['lft'] + $del2;
            $pageArray['lft'] = $pageUnder['rgt'] + 1;
            $pageArray['rgt'] = $pageArray['lft'] + $del1;
            $end = 0;
            //save new order
            $repositor->postOrderTraversal($pageUnder, $pageUnder['lft'], $end);
            $repositor->postOrderTraversal($pageArray, $pageArray['lft'], $end);
        }
    }

    /**
     * @param type $lft lft
     * @param type $rgt rgt
     *
     * @return type
     */
    public function getAllChildren($lft, $rgt)
    {
        return $this->getEntityManager()
                        ->getRepository($this->_entityName)
                        ->createQueryBuilder('c')
                        ->where('c.lft >= :lft')
                        ->andWhere('c.rgt != :rgt')
                        ->setParameters(array('lft' => $lft, 'rgt' => $rgt))
                        ->orderBy('c.lft', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

}
