<?php

/**
 * Controller class that manages the editing, saving and deleting of attributes groups that belong to the alpenite_blog_post entity (that has a entity type id of 15)
 * @author Enrico Naletto <enaletto@alpenite.com>
 */
class Alpenite_Blog_Adminhtml_GroupsController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__('Alpenite Blog Groups'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('alpenite_blog/adminhtml_groups'));
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('alpenite_blog/adminhtml_groups_grid')->toHtml()
        );
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $group = Mage::getModel('eav/entity_attribute_group');
        if ($groupId = $this->getRequest()->getParam('id', false)) {
            $group->load($groupId);

            if (!$group->getId()) {
                $this->_getSession()->addError($this->__('This group no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        Mage::register('current_group', $group);

        $groupEditBlock = $this->getLayout()->createBlock(
            'alpenite_blog/adminhtml_groups_edit'
        );

        $this->loadLayout()
            ->_addContent($groupEditBlock)
            ->renderLayout();
    }

    public function deleteAction() {
        $group = Mage::getModel('eav/entity_attribute_group');

        if ($groupId = $this->getRequest()->getParam('id', false)) {
            $group->load($groupId);

            if (!$group->getId()) {
                $this->_getSession()->addError($this->__('This group no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        try {
            $group->delete();

            $this->_getSession()->addSuccess(
                $this->__('The group has been deleted.')
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect(
            '*/*/index'
        );
    }

    public function massDeleteAction() {
        $groupIds = $this->getRequest()->getParam('groups');

        if (!is_array($groupIds)) {
            $this->_getSession()->addError($this->__('Please select some items.'));
        } else {
            try {
                $model = Mage::getModel('eav/entity_attribute_group');
                foreach ($groupIds as $groupId) {
                    $model->load($groupId)->delete();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d group(s) were deleted.', count($groupIds)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        return $this->_redirect('*/*/index');
    }

    public function saveAction() {
        $group = Mage::getModel('eav/entity_attribute_group');
        $groupData = $this->getRequest()->getPost();

        try {
            $groupId = $this->getRequest()->getParam('id', false);
            if ($groupId) {
                $group->load($groupId);
                if (!$group->getId()) {
                    $this->_getSession()->addError($this->__('This group no longer exists.'));
                    return $this->_redirect('*/*/index');
                }
            } else {
                //Save new group specific values
                $group->setData('sort_order', '1');
                $group->setData('default_id', '1');
            }

            $group->setData('attribute_group_name', $groupData['attribute_group_name']);
            $group->setData('attribute_set_id', $groupData['attribute_set_id']);

            $group->save();
            $this->_getSession()->addSuccess(
                $this->__('The group has been saved.'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*/index');
    }


    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('alpenite_blog/groups');
    }
}
