<?php

namespace CMS\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CMS\Bundle\AdminBundle\Entity\MediaCategory;
use CMS\Bundle\AdminBundle\Form\MediaCategoryType;

/**
 * MediaCategory controller.
 */
class MediaCategoryController extends Controller
{

    /**
     * index action
     *
     * @param type $page page
     *
     * @return type
     */
    public function indexAction($page)
    {
        $rep = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:MediaCategory");

        $total = $rep->getTotal();
        $perPage = $this->container->getParameter('per_item_page');;

        $lastPage = ceil($total / $perPage);
        $previousPage = $page > 1 ? $page - 1 : 1;
        $nextPage = $page < $lastPage ? $page + 1 : $lastPage;
        $entities = $rep->getList($perPage, ($page - 1) * $perPage);

        return $this->render('CMSAdminBundle:MediaCategory:index.html.twig', array(
            'entities' => $entities,
            'lastPage' => $lastPage,
            'previousPage' => $previousPage,
            'currentPage' => $page,
            'nextPage' => $nextPage,
            'total' => $total
        ));

    }

    /**
     * show action
     *
     * @param type $id the id
     *
     * @return type
     *
     * @throws type
     */
    public function showAction($id)
    {
        $entity = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:MediaCategory")
                    ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CMSAdminBundle:MediaCategory:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * new action
     *
     * @return type
     */
    public function newAction()
    {
        $entity = new MediaCategory();
        $form   = $this->createForm(new MediaCategoryType(), $entity);

        return $this->render('CMSAdminBundle:MediaCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * creata action
     *
     * @param \Symfony\Component\HttpFoundation\Request $request request
     *
     * @return type
     */
    public function createAction(Request $request)
    {
        $rep = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:MediaCategory");

        $entity  = new MediaCategory();
        $form = $this->createForm(new MediaCategoryType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $rep->addByEntity($entity);

            return $this->redirect(
                $this->generateUrl(
                    'admin_mediacategory_show',
                    array('id' => $entity->getId())
                )
            );
        }

        return $this->render('CMSAdminBundle:MediaCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * edit action
     *
     * @param type $id the id
     *
     * @return type
     *
     * @throws type
     */
    public function editAction($id)
    {
        $rep = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:MediaCategory");
        $entity = $rep->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaCategory entity.');
        }

        $editForm = $this->createForm(new MediaCategoryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CMSAdminBundle:MediaCategory:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * update action
     *
     * @param \Symfony\Component\HttpFoundation\Request $request request
     * @param type                                      $id      the id
     *
     * @return type
     *
     * @throws type
     */
    public function updateAction(Request $request, $id)
    {
        $rep = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:MediaCategory");

        $entity = $rep->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MediaCategoryType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $rep->addByEntity($entity);

            return $this->redirect(
                $this->generateUrl('admin_mediacategory_edit',
                    array('id' => $id)
                )
            );
        }

        return $this->render('CMSAdminBundle:MediaCategory:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * delete action
     *
     * @param \Symfony\Component\HttpFoundation\Request $request request
     * @param type                                      $id      the id
     *
     * @return type
     */
    public function deleteAction(Request $request, $id)
    {
        $rep = $this->getDoctrine()
                    ->getRepository("CMSAdminBundle:MediaCategory");

        $rep->deleteByIds(array($id));

        return $this->redirect(
            $this->generateUrl('admin_mediacategory')
        );
    }

    /**
     * create form delete
     *
     * @param type $id
     *
     * @return type
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}
