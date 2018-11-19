<?php

use yii\db\Migration;

/**
 * Class m181105_103630_objects_boundary_project
 */
class m181105_103635_lines extends Migration
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


        $this->createTable('{{%lines}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(36)->notNull()->unique(),
            'name' => $this->string(128)->notNull(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%lines_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'guid' => $this->string(36)->notNull(),
            'name' => $this->string(128)->notNull(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-lines_history-entity_id',"{{%lines_history}}",'entity_id',"{{%lines}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-lines_history-creator_id',"{{%lines_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-lines-version_id',"{{%lines}}",'version_id',"{{%lines_history}}",'id','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       
        $this->dropForeignKey('fk-lines-version_id',"{{%lines}}");
        $this->dropForeignKey('fk-lines_history-creator_id',"{{%lines_history}}");
        $this->dropForeignKey('fk-lines_history-entity_id',"{{%lines_history}}");

        $this->dropTable('{{%lines_history}}');
        $this->dropTable('{{%lines}}');
    }

}
