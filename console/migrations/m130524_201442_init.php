<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201442_init extends Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(128)->notNull(),
            'brigade_guid' => $this->string(32)->null(),
            'technic_guid' => $this->string(32)->null(),
            'ktu' => $this->float()->notNull()->defaultValue(0),
            'is_master' => $this->boolean()->notNull()->defaultValue(0),

            'login' => $this->string()->unique()->null(),
            'auth_key' => $this->string(32)->null(),
            'password_hash' => $this->string()->null(),
            'password_reset_token' => $this->string()->unique()->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10)
        ], $tableOptions);


        

        $this->createTable('{{%brigade}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(128)->notNull(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%brigade_history}}', [
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

        $this->addForeignKey('fk-brigade_history-entity_id',"{{%brigade_history}}",'entity_id',"{{%brigade}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-brigade_history-creator_id',"{{%brigade_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-brigade-version_id',"{{%brigade}}",'version_id',"{{%brigade_history}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-user-brigade_guid',"{{%user}}",'brigade_guid',"{{%brigade}}",'guid','CASCADE','CASCADE');

        $this->createTable('{{%technic}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(128)->notNull(),
            'marka' => $this->string(128)->null(),
            'number' => $this->string(128)->null(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%technic_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'guid' => $this->string(32)->notNull(),
            'name' => $this->string(128)->notNull(),
            'marka' => $this->string(128)->null(),
            'number' => $this->string(128)->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        

        $this->addForeignKey('fk-technic_history-entity_id',"{{%technic_history}}",'entity_id',"{{%technic}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-technic_history-creator_id',"{{%technic_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');
        
        $this->addForeignKey('fk-technic-version_id',"{{%technic}}",'version_id',"{{%technic_history}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-user-technic_guid',"{{%user}}",'technic_guid',"{{%technic}}",'guid','CASCADE','CASCADE');
    }


    public function safeDown()
    {
        $this->dropForeignKey('fk-user-brigade_guid',"{{%user}}");
        $this->dropForeignKey('fk-user-technic_guid',"{{%user}}");

        $this->dropForeignKey('fk-brigade-version_id',"{{%brigade}}");
        $this->dropForeignKey('fk-brigade_history-creator_id',"{{%brigade_history}}");
        $this->dropForeignKey('fk-brigade_history-entity_id',"{{%brigade_history}}");

        $this->dropForeignKey('fk-technic-version_id',"{{%technic}}");
        $this->dropForeignKey('fk-technic_history-creator_id',"{{%technic_history}}");
        $this->dropForeignKey('fk-technic_history-entity_id',"{{%technic_history}}");

        $this->dropTable('{{%brigade_history}}');
        $this->dropTable('{{%brigade}}');
        $this->dropTable('{{%technic_history}}');
        $this->dropTable('{{%technic}}');
        $this->dropTable('{{%user}}');
    }
}
