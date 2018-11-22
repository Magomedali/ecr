<?php

use yii\db\Migration;

/**
 * Class m181105_191709_requests
 */
class m181105_191709_requests extends Migration
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


        $this->createTable('{{%requests}}', [
            'id' => $this->primaryKey(),
            'created_at'=>$this->timestamp(),
            'completed_at'=>$this->timestamp(),
            'request'=>$this->string(50)->notNull(),
            'params_in'=>$this->text(50)->null(),
            'params_out'=>$this->text(50)->null(),
            'result'=>$this->boolean()->null()->defaultValue(0),
            'completed'=>$this->boolean()->null()->defaultValue(0),
            'raport_id'=>$this->integer()->null(),
            'user_id'=> $this->integer()->null(),
            'actor_id'=> $this->integer()->null(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%requests_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'created_at'=>$this->timestamp(),
            'completed_at'=>$this->timestamp(),
            'request'=>$this->string(50)->notNull(),
            'params_in'=>$this->text(50)->null(),
            'params_out'=>$this->text(50)->null(),
            'result'=>$this->boolean()->null()->defaultValue(0),
            'completed'=>$this->boolean()->null()->defaultValue(0),
            'raport_id'=>$this->integer()->null(),
            'user_id'=>$this->integer()->null(),
            'actor_id'=> $this->integer()->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-requests_history-entity_id',"{{%requests_history}}",'entity_id',"{{%requests}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-requests_history-creator_id',"{{%requests_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-requests-version_id',"{{%requests}}",'version_id',"{{%requests_history}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-requests-raport_id',"{{%requests}}",'raport_id',"{{%raport}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-requests-user_id',"{{%requests}}",'actor_id',"{{%user}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-requests-actor_id',"{{%requests}}",'actor_id',"{{%user}}",'id','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       
        $this->dropForeignKey('fk-requests-actor_id',"{{%requests}}");
        $this->dropForeignKey('fk-requests-user_id',"{{%requests}}");
        $this->dropForeignKey('fk-requests-raport_id',"{{%requests}}");
        $this->dropForeignKey('fk-requests-version_id',"{{%requests}}");
        $this->dropForeignKey('fk-requests_history-creator_id',"{{%requests_history}}");
        $this->dropForeignKey('fk-requests_history-entity_id',"{{%requests_history}}");

        $this->dropTable('{{%requests_history}}');
        $this->dropTable('{{%requests}}');
    }

 
}
