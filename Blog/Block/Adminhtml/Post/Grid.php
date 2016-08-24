<?php

class Alpenite_Blog_Block_Adminhtml_Post_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();
        $this->setId('alpenite_blog_grid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $posts = Mage::getModel('alpenite_blog/post')->getCollection()
            ->addAttributeToSelect('title')
            ->addAttributeToSelect('author');
        $this->setCollection($posts);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $helper = Mage::helper('alpenite_blog');

        $this->addColumn('title', array(
            'header' => $helper->__('Title'),
            'type' => 'varchar',
            'index' => 'title'
        ));

        $this->addColumn('author', array(
            'header' => $helper->__('Author'),
            'type' => 'varchar',
            'index' => 'author'
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('post');

        // delete en mass
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => $this->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => $this->__('Are you sure?')
        ));

        return parent::_prepareMassaction();
    }

    public function getGridUrl() {
        // chiama l'azione grid che corrisponde al metodo getGrid nella classe JournalController
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row) {
        /**
         * When a grid row is clicked, this is where the user should
         * be redirected to - in our example, the method editAction of
         * AlpeniteController.php.
         */
        return $this->getUrl(
            '*/*/edit',
            array(
                'id' => $row->getId()
            )
        );
    }
}