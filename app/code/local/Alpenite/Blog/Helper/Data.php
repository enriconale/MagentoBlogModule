<?php

class Alpenite_Blog_Helper_Data extends Mage_Core_Helper_Abstract {
    //Returns an array that contains all the values needed for the multiselect of sets and groups.
    public function getArrayOfGroups() {

        $entityTypeId = Mage::getModel('eav/entity')->setType(Alpenite_Blog_Model_Post::ENTITY)->getTypeId();

        $sets = Mage::getModel('eav/entity_attribute_set')
            ->getResourceCollection()
            ->setEntityTypeFilter($entityTypeId)
            ->load();

        $resultArray = array();
        foreach ($sets as $set) {
            $setId = $set->getAttributeSetId();
            $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();

            $groupIds = array();
            foreach ($groups as $group) {
                $groupIds[] = array(
                    'value' => $setId . '_' . $group->getId(),
                    'label' => Mage::helper('eav')->__($group->getAttributeGroupName())
                );
            }

            $resultArray[] = array(
                'value' => $groupIds,
                'label' => Mage::helper('eav')->__($set->getAttributeSetName())
            );
        }

        return $resultArray;
    }
    //Given an attribute id, returns an array containing all the ids of the groups to whom the attribute belongs.
    public function getGroupsIdsOfAttribute($attributeId) {
        $entityTypeId = Mage::getModel('eav/entity')->setType(Alpenite_Blog_Model_Post::ENTITY)->getTypeId();

        $sets = Mage::getModel('eav/entity_attribute_set')
            ->getResourceCollection()
            ->setEntityTypeFilter($entityTypeId)
            ->load();

        $ownerGroupsIdsOfAttribute = array();
        foreach ($sets as $set) {
            $setId = $set->getAttributeSetId();
            $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();


            foreach ($groups as $group) {
                $attributes = Mage::getModel('eav/entity_attribute')
                    ->getCollection()
                    ->setAttributeGroupFilter($group->getId())
                    ->load();


                foreach ($attributes as $attr) {
                    if ($attr->getId() == $attributeId) {
                        $ownerGroupsIdsOfAttribute[] = $setId . '_' . $group->getId();
                    }
                }
            }
        }

        return $ownerGroupsIdsOfAttribute;
    }

    //Returns all the sets that belong to the alpenite_blog_post entity.
    public function getAllSets() {
        $entityTypeId = Mage::getModel('eav/entity')->setType(Alpenite_Blog_Model_Post::ENTITY)->getTypeId();

        $sets = Mage::getModel('eav/entity_attribute_set')
            ->getResourceCollection()
            ->setEntityTypeFilter($entityTypeId)
            ->load();

        $resultArray = array();
        foreach ($sets as $set) {
            $resultArray[] = array(
                'value' => $set->getId(),
                'label' => $set->getAttributeSetName()
            );
        }
        return $resultArray;
    }

    //Given a frontend input, returns a valid backend type to store the information into the database.
    public function getBackendType($frontendInput) {
        switch ($frontendInput) {
            case 'text':
                return 'varchar';
            case 'textarea':
                return 'text';
            case 'boolean':
                return 'int';
            default:
                return 'varchar';
        }
    }

}