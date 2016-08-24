<?php

class Alpenite_Blog_IndexController extends Mage_Core_Controller_Front_Action {
    public function indexAction() {

        $posts = Mage::getModel('alpenite_blog/post')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->load();
        foreach ($posts as $post) {
            print "{$post->getTitle()}: {$post->getContent()}<br>";
        }
    }
}