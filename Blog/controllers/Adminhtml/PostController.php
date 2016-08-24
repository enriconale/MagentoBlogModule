<?php

class Alpenite_Blog_Adminhtml_PostController extends Mage_Adminhtml_Controller_Action {
    public function indexAction() {
        $this->_title($this->__('Alpenite Posts'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('alpenite_blog/adminhtml_post'));
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('alpenite_blog/adminhtml_post_grid')->toHtml()
        );
    }

    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * This action handles both viewing and editing existing posts.
     */
    public function editAction() {
        $post = Mage::getModel('alpenite_blog/post');
        if ($postId = $this->getRequest()->getParam('id', false)) {
            $post->load($postId);

            if (!$post->getId()) {
                $this->_getSession()->addError($this->__('This post no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        // Make the current brand object available to blocks.
        Mage::register('current_post', $post);

        // Instantiate the form container.
        $postEditBlock = $this->getLayout()->createBlock(
            'alpenite_blog/adminhtml_post_edit'
        );


        // Add the form container as the only item on this page.
        $this->loadLayout()
            ->_addContent($postEditBlock)
            ->renderLayout();
    }

    public function deleteAction() {
        $post = Mage::getModel('alpenite_blog/post');

        if ($postId = $this->getRequest()->getParam('id', false)) {
            $post->load($postId);

            if (!$post->getId()) {
                $this->_getSession()->addError($this->__('This post no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        try {
            $post->delete();

            $this->_getSession()->addSuccess(
                $this->__('The post has been deleted.')
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect(
            '*/*/index'
        );
    }

    public function saveAction() {
        $post = Mage::getModel('alpenite_blog/post');

        if ($postId = $this->getRequest()->getParam('id', false)) {
            $post->load($postId);

            if (!$post->getId()) {
                $this->_getSession()->addError($this->__('This post no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        $postData = $this->getRequest()->getPost();
        try {
            $post->addData($postData);
            $post->save();

            $this->_getSession()->addSuccess(
                $this->__('The post has been saved.')
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect(
            '*/*/index'
        );

    }

    public function massDeleteAction() {
        $postsIds = $this->getRequest()->getParam('post');

        if (!is_array($postsIds)) {
            $this->_getSession()->addError($this->__('Please select some items.'));
        } else {
            try {
                $model = Mage::getModel('alpenite_blog/post');
                foreach ($postsIds as $postId) {
                    $model->load($postId)->delete();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d post(s) were deleted.', count($postsIds)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        return $this->_redirect('*/*/index');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('alpenite_blog/post');
    }
}