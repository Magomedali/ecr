<?php

use yii\db\Migration;

/**
 * Class m181105_114939_typeofwork
 */
class m181105_114939_typeofwork extends Migration
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


        $this->createTable('{{%typeofwork}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(128)->notNull(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%typeofwork_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'guid' => $this->string(32)->notNull(),
            'name' => $this->string(128)->notNull(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-typeofwork_history-entity_id',"{{%typeofwork_history}}",'entity_id',"{{%typeofwork}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-typeofwork_history-creator_id',"{{%typeofwork_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-typeofwork-version_id',"{{%typeofwork}}",'version_id',"{{%typeofwork_history}}",'id','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       
        $this->dropForeignKey('fk-typeofwork-version_id',"{{%typeofwork}}");
        $this->dropForeignKey('fk-typeofwork_history-creator_id',"{{%typeofwork_history}}");
        $this->dropForeignKey('fk-typeofwork_history-entity_id',"{{%typeofwork_history}}");

        $this->dropTable('{{%typeofwork_history}}');
        $this->dropTable('{{%typeofwork}}');
    }
}
