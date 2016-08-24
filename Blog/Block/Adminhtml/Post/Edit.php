<?php

class Alpenite_Blog_Block_Adminhtml_Post_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    protected function _construct() {
        $this->_blockGroup = 'alpenite_blog';
        $this->_controller = 'adminhtml_post';

        $this->_mode = 'edit';

        $newOrEdit = $this->getRequest()->getParam('id')
            ? $this->__('Edit')
            : $this->__('New');
        $this->_headerText = $newOrEdit . ' ' . $this->__('Post');
    }
}