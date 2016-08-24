<?php

class Alpenite_Blog_Block_Adminhtml_Post_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        // Instantiate a new form to display our post for editing.
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
        ));

        $data = Mage::registry('current_post')->getData();

        $fieldset = $form->addFieldset(
            'general',
            array('legend' => Mage::helper('alpenite_blog')->__('Post Info')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('alpenite_blog')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title'
        ));

        $fieldset->addField('author', 'text', array(
            'label' => Mage::helper('alpenite_blog')->__('Author'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'author'
        ));

        $fieldset->addField('content', 'textarea', array(
            'label' => Mage::helper('alpenite_blog')->__('Content'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'content'
        ));

//        if (Mage::getSingleton('admin/session')->isAllowed('alpenite_blog/newattribute')) {
//                $headerBar = $this->getLayout()->createBlock('alpenite_blog/adminhtml_pogo_edit_attributes_create');
//                $fieldset->setHeaderBar($headerBar->toHtml());
//        }

        $form->setUseContainer(true);
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}