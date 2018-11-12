<?php

use yii\db\Migration;

/**
 * Class m181105_133127_raport_materials
 */
class m181105_133127_raport_materials extends Migration
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


        $this->createTable('{{%raport_materials}}', [
            'id' => $this->primaryKey(),

            'raport_id' => $this->integer()->notNull(),

            'nomenclature_guid' => $this->string(32)->notNull(),

            'was'=>$this->float()->notNull()->defaultValue(0),
            'spent'=> $this->float()->notNull()->defaultValue(0),
            'rest'=>$this->float()->notNull()->defaultValue(0),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%raport_materials_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'raport_id' => $this->integer()->notNull(),
            
            'nomenclature_guid' => $this->string(32)->notNull(),

            'was'=>$this->float()->notNull()->defaultValue(0),
            'spent'=> $this->float()->notNull()->defaultValue(0),
            'rest'=>$this->float()->notNull()->defaultValue(0),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-raport_materials_history-entity_id',"{{%raport_materials_history}}",'entity_id',"{{%raport_materials}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_materials_history-creator_id',"{{%raport_materials_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-raport_materials-version_id',"{{%raport_materials}}",'version_id',"{{%raport_materials_history}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-raport_materials-raport_id',"{{%raport_materials}}",'raport_id',"{{%raport}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_materials-nomenclature_guid',"{{%raport_materials}}",'nomenclature_guid',"{{%nomenclature}}",'guid','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-raport_materials-nomenclature_guid',"{{%raport_materials}}");
        $this->dropForeignKey('fk-raport_materials-raport_id',"{{%raport_materials}}");
        $this->dropForeignKey('fk-raport_materials-version_id',"{{%raport_materials}}");

        $this->dropForeignKey('fk-raport_materials_history-creator_id',"{{%raport_materials_history}}");
        $this->dropForeignKey('fk-raport_materials_history-entity_id',"{{%raport_materials_history}}");

        $this->dropTable('{{%raport_materials_history}}');
        $this->dropTable('{{%raport_materials}}');
    }
}
