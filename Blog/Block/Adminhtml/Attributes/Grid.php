<?php

/**
 * Description of Grid
 *
 * @author Enrico
 */
class Alpenite_Blog_Block_Adminhtml_Attributes_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();
        $this->setId('alpenite_blog_grid');
        $this->setDefaultSort('attribute_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $entityTypeId = Mage::getModel('eav/entity')->setType(Alpenite_Blog_Model_Post::ENTITY)->getTypeId();
        $collection = Mage::getModel('eav/entity_attribute')
            ->getCollection()
            ->addFieldToFilter('entity_type_id', $entityTypeId);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $helper = Mage::helper('eav');

        $this->addColumn('attribute_id', array(
            'header' => $helper->__('Attribute Id'),
            'type' => 'integer',
            'index' => 'attribute_id'
        ));

        $this->addColumn('entity_type_id', array(
            'header' => $helper->__('Entity Type Id'),
            'type' => 'integer',
            'index' => 'entity_type_id'
        ));

        $this->addColumn('attribute_code', array(
            'header' => $helper->__('Attribute Code'),
            'type' => 'varchar',
            'index' => 'attribute_code'
        ));

        $this->addColumn('backend_type', array(
            'header' => $helper->__('Backend Type'),
            'type' => 'varchar',
            'index' => 'backend_type'
        ));

        $this->addColumn('frontend_input', array(
            'header' => $helper->__('Frontend Input'),
            'type' => 'varchar',
            'index' => 'frontend_input'
        ));

        $this->addColumn('frontend_label', array(
            'header' => $helper->__('Frontend Label'),
            'type' => 'varchar',
            'index' => 'frontend_label'
        ));

        parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('attributes');

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
