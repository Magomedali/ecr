<?php

use yii\db\Migration;

/**
 * Class m190119_155959_materials_app_item
 */
class m190119_155959_materials_app_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {


        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%materials_app_item}}', [
            'id' => $this->primaryKey(),

            'material_app_id' => $this->integer()->notNull(),
            'nomenclature_guid' => $this->string(36)->notNull(),
            'count'=>$this->float()->notNull()->defaultValue(0),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%materials_app_item_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'material_app_id' => $this->integer()->notNull(),
            'nomenclature_guid' => $this->string(36)->notNull(),
            'count'=>$this->float()->notNull()->defaultValue(0),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-materials_app_item_history-entity_id',"{{%materials_app_item_history}}",'entity_id',"{{%materials_app_item}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-materials_app_item_history-creator_id',"{{%materials_app_item_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-materials_app_item-version_id',"{{%materials_app_item}}",'version_id',"{{%materials_app_item_history}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-materials_app_item-material_app_id',"{{%materials_app_item}}",'material_app_id',"{{%materials_app}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-materials_app_item-nomenclature_guid',"{{%materials_app_item}}",'nomenclature_guid',"{{%nomenclature}}",'guid','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-materials_app_item-nomenclature_guid',"{{%materials_app_item}}");
        $this->dropForeignKey('fk-materials_app_item-material_app_id',"{{%materials_app_item}}");
        $this->dropForeignKey('fk-materials_app_item-version_id',"{{%materials_app_item}}");

        $this->dropForeignKey('fk-materials_app_item_history-creator_id',"{{%materials_app_item_history}}");
        $this->dropForeignKey('fk-materials_app_item_history-entity_id',"{{%materials_app_item_history}}");

        $this->dropTable('{{%materials_app_item_history}}');
        $this->dropTable('{{%materials_app_item}}');
    }
}
