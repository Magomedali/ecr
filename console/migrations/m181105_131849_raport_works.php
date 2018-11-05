<?php

use yii\db\Migration;

/**
 * Class m181105_131849_raport_works
 */
class m181105_131849_raport_works extends Migration
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


        $this->createTable('{{%raport_works}}', [
            'id' => $this->primaryKey(),

            'raport_id' => $this->integer()->notNull(),
            'work_guid' => $this->string(32)->notNull(),
            'line_guid'=>  $this->string(32)->notNull(),
            'mechanized'=>$this->boolean()->defaultValue(0),
            'length'=> $this->float()->notNull()->defaultValue(0),
            'count'=>$this->float()->notNull()->defaultValue(0),
            'squaremeter'=>$this->float()->notNull()->defaultValue(0),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%raport_works_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'raport_id' => $this->integer()->notNull(),
            'work_guid' => $this->string(32)->notNull(),
            'line_guid'=>  $this->string(32)->notNull(),
            'mechanized'=>$this->boolean()->defaultValue(0),
            'length'=> $this->float()->notNull()->defaultValue(0),
            'count'=>$this->float()->notNull()->defaultValue(0),
            'squaremeter'=>$this->float()->notNull()->defaultValue(0),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-raport_works_history-entity_id',"{{%raport_works_history}}",'entity_id',"{{%raport_works}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_works_history-creator_id',"{{%raport_works_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-raport_works-version_id',"{{%raport_works}}",'version_id',"{{%raport_works_history}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_works-raport_id',"{{%raport_works}}",'raport_id',"{{%raport}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_works-work_guid',"{{%raport_works}}",'work_guid',"{{%typeofwork}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_works-line_guid',"{{%raport_works}}",'line_guid',"{{%lines}}",'guid','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-raport_works-line_guid',"{{%raport_works}}");
        $this->dropForeignKey('fk-raport_works-work_guid',"{{%raport_works}}");
        $this->dropForeignKey('fk-raport_works-raport_id',"{{%raport_works}}");
        $this->dropForeignKey('fk-raport_works-version_id',"{{%raport_works}}");

        $this->dropForeignKey('fk-raport_works_history-creator_id',"{{%raport_works_history}}");
        $this->dropForeignKey('fk-raport_works_history-entity_id',"{{%raport_works_history}}");

        $this->dropTable('{{%raport_works_history}}');
        $this->dropTable('{{%raport_works}}');
    }
}
