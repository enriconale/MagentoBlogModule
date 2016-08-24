<?php

/**
 * Controller class that manages the editing, saving and deleting of attributes sets that belong to the alpenite_blog_post entity (that has a entity type id of 15)
 * @author Enrico Naletto <enaletto@alpenite.com>
 */
class Alpenite_Blog_Adminhtml_SetController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__('Alpenite Blog Sets'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('alpenite_blog/adminhtml_set'));
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('alpenite_blog/adminhtml_set_grid')->toHtml()
        );
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $set = Mage::getModel('eav/entity_attribute_set');
        if ($setId = $this->getRequest()->getParam('id', false)) {
            $set->load($setId);

            if (!$set->getId()) {
                $this->_getSession()->addError($this->__('This set no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        Mage::register('current_set', $set);

        $setEditBlock = $this->getLayout()->createBlock(
            'alpenite_blog/adminhtml_set_edit'
        );

        $this->loadLayout()
            ->_addContent($setEditBlock)
            ->renderLayout();
    }

    public function deleteAction() {
        $set = Mage::getModel('eav/entity_attribute_set');

        if ($setId = $this->getRequest()->getParam('id', false)) {
            $set->load($setId);

            if (!$set->getId()) {
                $this->_getSession()->addError($this->__('This set no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        try {
            $set->delete();

            $this->_getSession()->addSuccess(
                $this->__('The set has been deleted.')
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
        $setIds = $this->getRequest()->getParam('set');

        if (!is_array($setIds)) {
            $this->_getSession()->addError($this->__('Please select some items.'));
        } else {
            try {
                $model = Mage::getModel('eav/entity_attribute_set');
                foreach ($setIds as $setId) {
                    $model->load($setId)->delete();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d set(s) were deleted.', count($setIds)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        return $this->_redirect('*/*/index');
    }

    public function saveAction() {
        $set = Mage::getModel('eav/entity_attribute_set');
        $setData = $this->getRequest()->getPost();

        try {
            $setId = $this->getRequest()->getParam('id', false);
            if ($setId) {
                $set->load($setId);
                if (!$set->getId()) {
                    $this->_getSession()->addError($this->__('This set no longer exists.'));
                    return $this->_redirect('*/*/index');
                }
            } else {
                //Save new set specific values
                $set->setData('entity_type_id', '15');
                $set->setData('sort_order', '2');
            }

            $set->setData('attribute_set_name', $setData['attribute_set_name']);

            $set->save();
            $this->_getSession()->addSuccess(
                $this->__('The set has been saved.'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*/index');
    }


    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('alpenite_blog/set');
    }
}
