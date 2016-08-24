<?php

/**
 * Observer class that modifies the 'modified_by' attribute automatically with the username of the admin user that modified the associated post.
 *
 * @author Enrico
 */
class Alpenite_Blog_Model_Observer {

    public function setModifiedBy($observer) {
        $currentPost = $observer->getEvent()->getObject();
        $currentUsername = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $currentPost->setModifiedBy($currentUsername . ' on ' . date('Y-m-d H:i:s'));
    }
}
