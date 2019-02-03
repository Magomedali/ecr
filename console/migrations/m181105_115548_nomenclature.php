<?php

use yii\db\Migration;

/**
 * Class m181105_115548_nomenclature
 */
class m181105_115548_nomenclature extends Migration
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


        $this->createTable('{{%nomenclature}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(36)->notNull()->unique(),
            'name' => $this->string(255)->notNull(),
            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%nomenclature_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'guid' => $this->string(36)->notNull(),
            'name' => $this->string(255)->notNull(),
            
            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-nomenclature_history-entity_id',"{{%nomenclature_history}}",'entity_id',"{{%nomenclature}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-nomenclature_history-creator_id',"{{%nomenclature_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-nomenclature-version_id',"{{%nomenclature}}",'version_id',"{{%nomenclature_history}}",'id','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       
        $this->dropForeignKey('fk-nomenclature-version_id',"{{%nomenclature}}");
        $this->dropForeignKey('fk-nomenclature_history-creator_id',"{{%nomenclature_history}}");
        $this->dropForeignKey('fk-nomenclature_history-entity_id',"{{%nomenclature_history}}");

        $this->dropTable('{{%nomenclature_history}}');
        $this->dropTable('{{%nomenclature}}');


    }
}
