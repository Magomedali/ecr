<?php

use yii\db\Migration;

/**
 * Class m190130_153513_raport_of_regulatory_works
 */
class m190130_153513_raport_of_regulatory_works extends Migration
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


        $this->createTable('{{%raport_regulatory_works}}', [
            'id' => $this->primaryKey(),

            'raport_regulatory_id' => $this->integer()->notNull(),
            'user_guid'=>  $this->string(36)->notNull(),
            'work_guid' => $this->string(36)->notNull(),
            'hours'=>$this->float()->notNull()->defaultValue(0),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%raport_regulatory_works_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'raport_regulatory_id' => $this->integer()->notNull(),
            'user_guid'=>  $this->string(36)->notNull(),
            'work_guid' => $this->string(36)->notNull(),
            'hours'=>$this->float()->notNull()->defaultValue(0),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-raport_regulatory_works_history-entity_id',"{{%raport_regulatory_works_history}}",'entity_id',"{{%raport_regulatory_works}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_regulatory_works_history-creator_id',"{{%raport_regulatory_works_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-raport_regulatory_works-version_id',"{{%raport_regulatory_works}}",'version_id',"{{%raport_regulatory_works_history}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-raport_regulatory_works-raport_regulatory_id',"{{%raport_regulatory_works}}",'raport_regulatory_id',"{{%raport_regulatory}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_regulatory_works-work_guid',"{{%raport_regulatory_works}}",'work_guid',"{{%typeofwork}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_regulatory_works-user_guid',"{{%raport_regulatory_works}}",'user_guid',"{{%user}}",'guid','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-raport_regulatory_works-user_guid',"{{%raport_regulatory_works}}");
        $this->dropForeignKey('fk-raport_regulatory_works-work_guid',"{{%raport_regulatory_works}}");
        $this->dropForeignKey('fk-raport_regulatory_works-raport_regulatory_id',"{{%raport_regulatory_works}}");
        $this->dropForeignKey('fk-raport_regulatory_works-version_id',"{{%raport_regulatory_works}}");

        $this->dropForeignKey('fk-raport_regulatory_works_history-creator_id',"{{%raport_regulatory_works_history}}");
        $this->dropForeignKey('fk-raport_regulatory_works_history-entity_id',"{{%raport_regulatory_works_history}}");

        $this->dropTable('{{%raport_regulatory_works_history}}');
        $this->dropTable('{{%raport_regulatory_works}}');
    }
}
