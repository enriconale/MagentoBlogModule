<?php

class Alpenite_Blog_Block_Adminhtml_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element {
    /**
     * Initialize block template
     */
    protected function _construct() {
        $this->setTemplate('alpencms/form/renderer/fieldset/element.phtml');
    }

    /**
     * Retireve associated with element attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttribute() {
        return Mage::getSingleton("eav/config")->getAttribute('alpenite_blog_post', $this->getElement()->getName());
    }

    /**
     * Check "Use default" checkbox display availability
     *
     * @return bool
     */
    public function canDisplayUseDefault() {
        $attributeId = $this->getAttribute()->getAttributeId();
        if ($attributeId == null) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('alpenite_blog_post', $this->getAttributeCode());
            $attributeId = $attribute->getAttributeId();
        } else {
            $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        }

        if (Mage::registry('alpenite_blog_form_store') != '0' && $attribute) {
            if ($attribute->getNote() == Alpenite_AlpenCms_Model_Cms::ALPENCMS_SCOPE_GLOBAL) {
                $this->getElement()->setDisabled(true);
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Check default value usage fact
     *
     * @return bool
     */
    public function usedDefault() {
        $attributeId = $this->getAttribute()->getAttributeId();
        if ($attributeId == null) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('alpenite_blog_post', $this->getAttributeCode());
            $attributeId = $attribute->getAttributeId();
        } else {
            $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        }

        $table = $attribute->getBackend()->getTable();

        $defaultValue = $this->getAttributeDefaultValue($table, $attributeId, $this->getEntityId());

        if ($this->getElement()->getValue() == $defaultValue && Mage::registry('alpenite_blog_form_store') != $this->_getDefaultStoreId()) {
            return true;
        }
        if ($defaultValue === false && !$this->getAttribute()->getIsRequired() && $this->getElement()->getValue()) {
            return false;
        }
        return $defaultValue === false;
    }

    /**
     * Disable field in default value using case
     *
     * @return Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
     */
    public function checkFieldDisable() {
        if ($this->canDisplayUseDefault() && $this->usedDefault()) {
            $this->getElement()->setDisabled(true);
        }
        return $this;
    }

    /**
     * Retrieve label of attribute scope
     *
     * GLOBAL | WEBSITE | STORE
     *
     * @return string
     */
    public function getScopeLabel() {
        $html = '';
        $attributeId = $this->getAttribute()->getAttributeId();
        if ($attributeId == null) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('alpenite_blog_post', $this->getAttributeCode());
            $attributeId = $attribute->getAttributeId();
        } else {
            $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        }

        if (!$attribute || Mage::app()->isSingleStoreMode()) {
            return $html;
        }

        if ($attribute->getNote() == Alpenite_AlpenCms_Model_Cms::ALPENCMS_SCOPE_GLOBAL) {
            $html .= Mage::helper('adminhtml')->__('[GLOBAL]');
        } else if ($attribute->getNote() == Alpenite_AlpenCms_Model_Cms::ALPENCMS_SCOPE_WEBSITE) {
            $html .= Mage::helper('adminhtml')->__('[WEBSITE]');
        } else if ($attribute->getNote() == Alpenite_AlpenCms_Model_Cms::ALPENCMS_SCOPE_STORE) {
            $html .= Mage::helper('adminhtml')->__('[STORE VIEW]');
        }

        return $html;
    }

    /**
     * Retrieve element label html
     *
     * @return string
     */
    public function getElementLabelHtml() {
        $element = $this->getElement();
        $label = $element->getLabel();
        if (!empty($label)) {
            $element->setLabel($this->__($label));
        }
        return $element->getLabelHtml();
    }

    /**
     * Retrieve element html
     *
     * @return string
     */
    public function getElementHtml() {
        return $this->getElement()->getElementHtml();
    }

    /**
     * Default sore ID getter
     *
     * @return integer
     */
    protected function _getDefaultStoreId() {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID; // 0
    }

    private function getAttributeDefaultValue($table, $attribute_id, $entity_id) {
        return Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne(
            'SELECT value FROM ' . $table . ' WHERE attribute_id = ' . $attribute_id . ' AND store_id = 0  AND entity_id = ' . $entity_id . ';'
        );
    }
}
