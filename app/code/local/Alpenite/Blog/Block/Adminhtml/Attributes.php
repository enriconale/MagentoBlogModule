<?php

class Alpenite_Blog_Block_Adminhtml_Attributes extends Mage_Adminhtml_Block_Widget_Grid_Container {
    public function __construct() {
        $this->_blockGroup = 'alpenite_blog';
        $this->_controller = 'adminhtml_attributes';
        $this->_headerText = Mage::helper('alpenite_blog')->__('Alpenite Blog Attributes');

        parent::__construct();
    }
}
