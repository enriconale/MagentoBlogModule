<?php
$installer = $this;
$installer->startSetup();
/*
 * Create all entity tables
 */
$installer->createEntityTables(
    $this->getTable('alpenite_blog/post_entity')
);

/*
 * Add Entity type
 */
$installer->addEntityType('alpenite_blog_post', Array(
    'entity_model' => 'alpenite_blog/post',
    'attribute_model' => '',
    'table' => 'alpenite_blog/post_entity',
    'increment_model' => '',
    'increment_per_store' => '0'
));
$installer->installEntities();
$installer->endSetup();