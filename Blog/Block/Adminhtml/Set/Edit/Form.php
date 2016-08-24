<?php

class Alpenite_Blog_Block_Adminhtml_Set_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
        ));

        $data = Mage::registry('current_set')->getData();

        $helper = Mage::helper('eav');

        $fieldset = $form->addFieldset(
            'general',
            array('legend' => Mage::helper('eav')->__('Set Info')));

        $fieldset->addField('attribute_set_name', 'text', array(
            'label' => Mage::helper('eav')->__('Set Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'attribute_set_name'
        ));

        $form->setUseContainer(true);
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}