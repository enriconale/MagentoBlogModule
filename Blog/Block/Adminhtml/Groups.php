<?php

class Alpenite_Blog_Block_Adminhtml_Groups extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_blockGroup = 'alpenite_blog';
        $this->_controller = 'adminhtml_groups';
        $this->_headerText = Mage::helper('alpenite_blog')->__('Alpenite Blog Groups');

        parent::__construct();
    }
}
