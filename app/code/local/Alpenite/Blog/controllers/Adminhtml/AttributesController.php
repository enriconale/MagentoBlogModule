<?php

/**
 * Controller class that manages the editing, saving and deleting of attributes that belong to the alpenite_blog_post entity (that has a entity type id of 15). The user can choose a set and consequently its groups to whom the attribute will belong to.
 * @author Enrico Naletto <enaletto@alpenite.com>
 */
class Alpenite_Blog_Adminhtml_AttributesController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__('Alpenite Blog Attributes'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('alpenite_blog/adminhtml_attributes'));
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('alpenite_blog/adminhtml_attributes_grid')->toHtml()
        );
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $attribute = Mage::getModel('eav/entity_attribute');
        $attributeId = $this->getRequest()->getParam('id', false);
        if ($attributeId) {
            $attribute->load($attributeId);

            if (!$attribute->getId()) {
                $this->_getSession()->addError($this->__('This attribute no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        $attribute->setData('attribute_group_ids', Mage::helper('alpenite_blog')->getGroupsIdsOfAttribute($attributeId));

        Mage::register('current_attribute', $attribute);

        $attributeEditBlock = $this->getLayout()->createBlock(
            'alpenite_blog/adminhtml_attributes_edit'
        );

        $this->loadLayout()
            ->_addContent($attributeEditBlock)
            ->renderLayout();
    }

    public function deleteAction() {
        $attribute = Mage::getModel('eav/entity_attribute');

        if ($attributeId = $this->getRequest()->getParam('id', false)) {
            $attribute->load($attributeId);

            if (!$attribute->getId()) {
                $this->_getSession()->addError($this->__('This attribute no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        try {
            $attribute->delete();

            $this->_getSession()->addSuccess(
                $this->__('The attribute has been deleted.')
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
        $attributesIds = $this->getRequest()->getParam('attributes');

        if (!is_array($attributesIds)) {
            $this->_getSession()->addError($this->__('Please select some items.'));
        } else {
            try {
                $model = Mage::getModel('eav/entity_attribute');
                foreach ($attributesIds as $attributeId) {
                    $model->load($attributeId)->delete();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d attribute(s) were deleted.', count($attributesIds)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        return $this->_redirect('*/*/index');
    }

    public function saveAction() {
        $attribute = Mage::getModel('eav/entity_attribute');
        $attributeData = $this->getRequest()->getPost();

        $setId = Mage::getModel('alpenite_blog/post')
            ->getResource()
            ->getEntityType()
            ->getDefaultAttributeSetId();

        try {
            $attributeId = $this->getRequest()->getParam('id', false);
            if ($attributeId) {
                //Check if the attribute to be edited exists.
                $attribute->load($attributeId);
                if (!$attribute->getId()) {
                    $this->_getSession()->addError($this->__('This attribute no longer exists.'));
                    return $this->_redirect('*/*/index');
                }
            } else {
                //Save new attribute specific values
                $entityTypeId = Mage::getModel('eav/entity')->setType(Alpenite_Blog_Model_Post::ENTITY)->getTypeId();
                $attribute->setData('entity_type_id', $entityTypeId);
                $attribute->setData('is_user_defined', '1');
            }

            //Save all the other values (that are the same both when creating a new attribute both when editing an existing one)
            $attribute->setData('attribute_code', $attributeData['attribute_code']);
            $attribute->setData('frontend_label', $attributeData['frontend_label']);
            $attribute->setData('frontend_input', $attributeData['frontend_input']);
            $attribute->setData('backend_type', Mage::helper('alpenite_blog')->getBackendType($attributeData['frontend_input']));
            $attribute->setData('is_required', $attributeData['is_required']);
            //$attribute->setAttributeSetId($setId);
            //$attribute->setAttributeGroupId($attributeData['attribute_group_ids'][0]);

            foreach ($attributeData['attribute_group_ids'] as $groupId) {
                $setAndGroup = explode('_', $groupId);
                $attribute->setAttributeSetId($setAndGroup[0]);
                $attribute->setAttributeGroupId($setAndGroup[1]);
                $attribute->save();
            }
            $this->_getSession()->addSuccess(
                $this->__('The attribute has been saved.'));

        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*/index');

    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('alpenite_blog/attributes');
    }
}