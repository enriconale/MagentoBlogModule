<?php

class Alpenite_Blog_IndexController extends Mage_Core_Controller_Front_Action {
    public function indexAction() {
        $posts = Mage::getModel('alpenite_blog/post')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->load();

        $this->loadLayout();
        $this->getLayout()->getBlock('post')->setData("posts_collection", $posts);
        $this->renderLayout();
    }
}