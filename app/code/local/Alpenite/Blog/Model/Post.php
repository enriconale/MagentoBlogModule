<?php

class Alpenite_Blog_Model_Post extends Mage_Core_Model_Abstract {

    const ENTITY = 'alpenite_blog_post';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'alpenite_blog_post';

    protected function _construct() {
        $this->_init('alpenite_blog/post');
    }
}