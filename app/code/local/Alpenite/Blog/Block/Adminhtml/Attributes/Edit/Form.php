<?php

class Alpenite_Blog_Block_Adminhtml_Attributes_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
        ));

        $data = Mage::registry('current_attribute')->getData();

        $helper = Mage::helper('eav');
        $frontendinput = array(
            array(
                'value' => 'text',
                'label' => $helper->__('Text Field')
            ),
            array(
                'value' => 'textarea',
                'label' => $helper->__('Text Area')
            ),
            array(
                'value' => 'boolean',
                'label' => $helper->__('Yes/No')
            ));

        $yesno = array(
            array(
                'value' => '1',
                'label' => Mage::helper('alpenite_blog')->__('Yes')
            ),
            array(
                'value' => '0',
                'label' => Mage::helper('alpenite_blog')->__('No')
            ));

        $fieldset = $form->addFieldset(
            'general',
            array('legend' => Mage::helper('eav')->__('Attribute Info')));

        $fieldset->addField('frontend_label', 'text', array(
            'label' => $helper->__('Frontend Label'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'frontend_label'
        ));

        $fieldset->addField('attribute_code', 'text', array(
            'label' => $helper->__('Attribute Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'attribute_code'
        ));

        $fieldset->addField('frontend_input', 'select', array(
            'label' => $helper->__('Input Type'),
            'required' => true,
            'name' => 'frontend_input',
            'values' => $frontendinput,
        ));

        $fieldset->addField('is_required', 'select', array(
            'label' => $helper->__('Required'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'is_required',
            'values' => $yesno,
        ));

        $fieldset->addField('attribute_group_ids', 'multiselect', array(
            'label' => $helper->__('Groups'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'attri    bute_group_ids',
            'values' => Mage::helper('alpenite_blog')->getArrayOfGroups(),
        ));

        $form->setUseContainer(true);
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
