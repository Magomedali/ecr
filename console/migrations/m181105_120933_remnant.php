<?php

use yii\db\Migration;

/**
 * Class m181105_120933_remnant
 */
class m181105_120933_remnant extends Migration
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


        $this->createTable('{{%remnant}}', [
            'id' => $this->primaryKey(),
            'brigade_guid' => $this->string(36)->notNull()->unique(),
            'updated_at'=>$this->timestamp(),
            'nomenclature_guid'=>$this->string(36)->notNull(),
            'count'=>$this->float()->notNull()->defaultValue(0),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%remnant_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'brigade_guid' => $this->string(36)->notNull(),
            'updated_at'=>$this->timestamp(),
            'nomenclature_guid'=>$this->string(36)->notNull(),
            'count'=>$this->float()->notNull()->defaultValue(0),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-remnant_history-entity_id',"{{%remnant_history}}",'entity_id',"{{%remnant}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-remnant_history-creator_id',"{{%remnant_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-remnant-version_id',"{{%remnant}}",'version_id',"{{%remnant_history}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-remnant-brigade_guid',"{{%remnant}}",'brigade_guid',"{{%brigade}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-remnant-nomenclature_guid',"{{%remnant}}",'nomenclature_guid',"{{%nomenclature}}",'guid','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-remnant-nomenclature_guid',"{{%remnant}}");
        $this->dropForeignKey('fk-remnant-brigade_guid',"{{%remnant}}");
        $this->dropForeignKey('fk-remnant-version_id',"{{%remnant}}");
        $this->dropForeignKey('fk-remnant_history-creator_id',"{{%remnant_history}}");
        $this->dropForeignKey('fk-remnant_history-entity_id',"{{%remnant_history}}");

        $this->dropTable('{{%remnant_history}}');
        $this->dropTable('{{%remnant}}');
    }
}
