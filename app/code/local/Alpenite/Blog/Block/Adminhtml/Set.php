<?php

class Alpenite_Blog_Block_Adminhtml_Set extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_blockGroup = 'alpenite_blog';
        $this->_controller = 'adminhtml_set';
        $this->_headerText = Mage::helper('alpenite_blog')->__('Alpenite Blog Sets');

        parent::__construct();
    }
}