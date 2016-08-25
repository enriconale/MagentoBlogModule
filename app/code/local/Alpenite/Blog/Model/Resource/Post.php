<?php

class Alpenite_Blog_Model_Resource_Post extends Mage_Eav_Model_Entity_Abstract {

    public function __construct() {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('alpenite_blog_post');
        $this->setConnection(
            $resource->getConnection('blog_read'),
            $resource->getConnection('blog_write'));
    }

    protected function _getDefaultAttributes() {
        return array(
            'entity_type_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'increment_id',
            'store_id',
            'website_id'
        );
    }

    /**
     * Check attribute unique value
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param Varien_Object $object
     * @return boolean
     */
    public function checkAttributeUniqueValue(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $object) {
        //recupero store id dal'oggetto
        $storeId = (int)Mage::app()->getStore($object->getStoreId())->getId();
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();
        if ($attribute->getBackend()->getType() === 'static') {
            $value = $object->getData($attribute->getAttributeCode());
            $bind = array(
                'entity_type_id' => $this->getTypeId(),
                'attribute_code' => trim($value)
            );

            $select
                ->from($this->getEntityTable(), $this->getEntityIdField())
                ->where('entity_type_id = :entity_type_id')
                ->where($attribute->getAttributeCode() . ' = :attribute_code');
        } else {
            $value = $object->getData($attribute->getAttributeCode());
            if ($attribute->getBackend()->getType() == 'datetime') {
                $date = new Zend_Date($value, Varien_Date::DATE_INTERNAL_FORMAT);
                $value = $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }
            $bind = array(
                'entity_type_id' => $this->getTypeId(),
                'attribute_id' => $attribute->getId(),
                'value' => trim($value),
                'store_id' => $storeId
            );
            $select
                ->from($attribute->getBackend()->getTable(), $attribute->getBackend()->getEntityIdField())
                ->where('entity_type_id = :entity_type_id')
                ->where('attribute_id = :attribute_id')
                ->where('store_id = :store_id')
                ->where('value = :value');
        }
        $data = $adapter->fetchCol($select, $bind);

        if ($object->getId()) {
            if (isset($data[0])) {
                return $data[0] == $object->getId();
            }
            return true;
        }

        return !count($data);
    }


    /**
     * Initialize attribute value for object
     *
     * @param   Varien_Object $object
     * @param   array $valueRow
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _setAttributeValue($object, $valueRow) {
        $attribute = $this->getAttribute($valueRow['attribute_id']);
        if ($attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $object->setData($attributeCode, $valueRow['value']);
            $attribute->getBackend()->setEntityValueId($object, $valueRow['value_id']);
        }

        return $this;
    }

    /**
     * Prepare entity object data for save
     *
     * result array structure:
     * array (
     *  'newObject', 'entityRow', 'insert', 'update', 'delete'
     * )
     *
     * @param   Varien_Object $newObject
     * @return  array
     */
    protected function _collectSaveData($newObject) {
        //recupero store id dal'oggetto
        $storeId = (int)Mage::app()->getStore($newObject->getStoreId())->getId();
        $newData = $newObject->getData();
        $entityId = $newObject->getData($this->getEntityIdField());

        // define result data
        $entityRow = array();
        $insert = array();
        $update = array();
        $delete = array();

        if (!empty($entityId)) {
            $origData = $newObject->getOrigData();
            /**
             * get current data in db for this entity if original data is empty
             */
            if (empty($origData)) {
                $origData = $this->_getOrigObject($newObject)->getOrigData();
            }

            /**
             * drop attributes that are unknown in new data
             * not needed after introduction of partial entity loading
             */
            foreach ($origData as $k => $v) {
                if (!array_key_exists($k, $newData)) {
                    unset($origData[$k]);
                }
            }
        } else {
            $origData = array();
        }

        $staticFields = $this->_getWriteAdapter()->describeTable($this->getEntityTable());
        $staticFields = array_keys($staticFields);
        $attributeCodes = array_keys($this->_attributesByCode);

        foreach ($newData as $k => $v) {
            /**
             * Check attribute information
             */
            if (is_numeric($k) || is_array($v)) {
                continue;
            }
            /**
             * Check if data key is presented in static fields or attribute codes
             */
            if (!in_array($k, $staticFields) && !in_array($k, $attributeCodes)) {
                continue;
            }

            $attribute = $this->getAttribute($k);
            if (empty($attribute)) {
                continue;
            }

            $attrId = $attribute->getAttributeId();

            /**
             * if attribute is static add to entity row and continue
             */
            if ($this->isAttributeStatic($k)) {
                $entityRow[$k] = $this->_prepareStaticValue($k, $v);
                continue;
            }

            /**
             * Check comparability for attribute value
             */
            if ($this->_canUpdateAttribute($attribute, $v, $origData)) {
                if ($this->_isAttributeValueEmpty($attribute, $v)) {
                    $delete[$attribute->getBackend()->getTable()][] = array(
                        'attribute_id' => $attrId,
                        'value_id' => $attribute->getBackend()->getEntityValueId($newObject),
                        'store_id' => $storeId
                    );
                } elseif ($v !== $origData[$k]) {
                    $update[$attrId] = array(
                        'value_id' => $attribute->getBackend()->getEntityValueId($newObject),
                        'value' => $v,
                    );
                }
            } else if (!$this->_isAttributeValueEmpty($attribute, $v)) {
                $insert[$attrId] = $v;
            }
        }

        $result = compact('newObject', 'entityRow', 'insert', 'update', 'delete');
        return $result;
    }


    /**
     * Save entity attribute value
     *
     * Collect for mass save
     *
     * @param Mage_Core_Model_Abstract $object
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param mixed $value
     * @return Mage_Eav_Model_Entity_Abstract
     */
    protected function _saveAttribute($object, $attribute, $value) {
        $storeId = (int)Mage::app()->getStore($object->getStoreId())->getId();
        $table = $attribute->getBackend()->getTable();
        if (!isset($this->_attributeValuesToSave[$table])) {
            $this->_attributeValuesToSave[$table] = array();
        }

        $entityIdField = $attribute->getBackend()->getEntityIdField();

        $data = array(
            'entity_type_id' => $object->getEntityTypeId(),
            $entityIdField => $object->getId(),
            'attribute_id' => $attribute->getId(),
            'value' => $this->_prepareValueForSave($value, $attribute),
            'store_id' => $storeId
        );

        $this->_attributeValuesToSave[$table][] = $data;

        return $this;
    }

    /**
     * Save attribute
     *
     * @param Varien_Object $object
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function saveAttribute(Varien_Object $object, $attributeCode) {
        $storeId = (int)Mage::app()->getStore($object->getStoreId())->getId();
        $attribute = $this->getAttribute($attributeCode);
        $backend = $attribute->getBackend();
        $table = $backend->getTable();
        $entity = $attribute->getEntity();
        $entityIdField = $entity->getEntityIdField();
        $adapter = $this->_getWriteAdapter();

        $row = array(
            'entity_type_id' => $entity->getTypeId(),
            'attribute_id' => $attribute->getId(),
            $entityIdField => $object->getData($entityIdField),
            'store_id' => $storeId
        );

        $newValue = $object->getData($attributeCode);
        if ($attribute->isValueEmpty($newValue)) {
            $newValue = null;
        }

        $whereArr = array();
        foreach ($row as $field => $value) {
            $whereArr[] = $adapter->quoteInto($field . '=?', $value);
        }
        $where = implode(' AND ', $whereArr);

        $adapter->beginTransaction();

        try {
            $select = $adapter->select()
                ->from($table, 'value_id')
                ->where($where);
            $origValueId = $adapter->fetchOne($select);

            if ($origValueId === false && ($newValue !== null)) {
                $this->_insertAttribute($object, $attribute, $newValue);
            } elseif ($origValueId !== false && ($newValue !== null)) {
                $this->_updateAttribute($object, $attribute, $origValueId, $newValue);
            } elseif ($origValueId !== false && ($newValue === null)) {
                $adapter->delete($table, $where);
            }
            $this->_processAttributeValues();
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        return $this;
    }
}