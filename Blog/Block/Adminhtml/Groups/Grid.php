<?php

class Alpenite_Blog_Block_Adminhtml_Groups_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {

        parent::__construct();
        $this->setId('alpenite_blog_grid');
        $this->setDefaultSort('attribute_group_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $entityTypeId = Mage::getModel('eav/entity')->setType(Alpenite_Blog_Model_Post::ENTITY)->getTypeId();

        $sets = Mage::getModel('eav/entity_attribute_set')
            ->getResourceCollection()
            ->setEntityTypeFilter($entityTypeId)
            ->load();

        $setIdsArray = array();
        foreach ($sets as $set) {
            $setIdsArray[] = $set->getId();
        }

        $collection = Mage::getModel('eav/entity_attribute_group')
            ->getCollection()
            ->addFieldToFilter('attribute_set_id', $setIdsArray);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $helper = Mage::helper('eav');

        $this->addColumn('attribute_group_id', array(
            'header' => $helper->__('Group Id'),
            'type' => 'integer',
            'index' => 'attribute_group_id'
        ));

        $this->addColumn('attribute_set_id', array(
            'header' => $helper->__('Set Id'),
            'type' => 'integer',
            'index' => 'attribute_set_id'
        ));

        $this->addColumn('attribute_group_name', array(
            'header' => $helper->__('Group Name'),
            'type' => 'varchar',
            'index' => 'attribute_group_name'
        ));

        parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('groups');

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
