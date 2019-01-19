<?php

use yii\db\Migration;

/**
 * Class m190119_143236_stockroom
 */
class m190119_143236_stockroom extends Migration
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


        $this->createTable('{{%stockroom}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(36)->notNull()->unique(),
            'name' => $this->string(255)->notNull(),
            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%stockroom_history}}', [
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

        
        $this->addForeignKey('fk-stockroom_history-entity_id',"{{%stockroom_history}}",'entity_id',"{{%stockroom}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-stockroom_history-creator_id',"{{%stockroom_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-stockroom-version_id',"{{%stockroom}}",'version_id',"{{%stockroom_history}}",'id','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       
        $this->dropForeignKey('fk-stockroom-version_id',"{{%stockroom}}");
        $this->dropForeignKey('fk-stockroom_history-creator_id',"{{%stockroom_history}}");
        $this->dropForeignKey('fk-stockroom_history-entity_id',"{{%stockroom_history}}");

        $this->dropTable('{{%stockroom_history}}');
        $this->dropTable('{{%stockroom}}');


    }
}
