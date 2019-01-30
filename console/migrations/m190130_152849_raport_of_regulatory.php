<?php

use yii\db\Migration;

/**
 * Class m190130_152849_raport_of_regulatory
 */
class m190130_152849_raport_of_regulatory extends Migration
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


        $this->createTable('{{%raport_regulatory}}', [
            'id' => $this->primaryKey(),

            'guid' => $this->string(36)->null()->unique(),
            'number'=>$this->string()->null(),
            'status'=>$this->integer()->null(),
            'created_at'=>$this->timestamp(),
            'starttime'=>$this->time()->null(),
            'endtime'=>$this->time()->null(),

            // 'brigade_guid' => $this->string(36)->notNull(),
            // 'object_guid' => $this->string(36)->notNull(),
            // 'boundary_guid' => $this->string(36)->null(),
            // 'project_guid' => $this->string(36)->notNull(),
            'master_guid'=>$this->string(36)->notNull(),
            'user_guid'=>$this->string(36)->notNull(),
            'comment' => $this->text()->null(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%raport_regulatory_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'guid' => $this->string(36)->null(),
            'number'=>$this->string()->null(),
            'status'=>$this->integer()->null(),
            'created_at'=>$this->timestamp()->null(),
            'starttime'=>$this->time()->null(),
            'endtime'=>$this->time()->null(),

            // 'brigade_guid' => $this->string(36)->notNull(),
            // 'object_guid' => $this->string(36)->notNull(),
            // 'boundary_guid' => $this->string(36)->null(),
            // 'project_guid' => $this->string(36)->notNull(),
            'master_guid'=>$this->string(36)->notNull(),
            'user_guid'=>$this->string(36)->notNull(),

            'comment' => $this->text()->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-raport_regulatory_history-entity_id',"{{%raport_regulatory_history}}",'entity_id',"{{%raport_regulatory}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_regulatory_history-creator_id',"{{%raport_regulatory_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-raport_regulatory-version_id',"{{%raport_regulatory}}",'version_id',"{{%raport_regulatory_history}}",'id','CASCADE','CASCADE');

        //$this->addForeignKey('fk-raport_regulatory-brigade_guid',"{{%raport_regulatory}}",'brigade_guid',"{{%brigade}}",'guid','CASCADE','CASCADE');
        //$this->addForeignKey('fk-raport_regulatory-object_guid',"{{%raport_regulatory}}",'object_guid',"{{%object}}",'guid','CASCADE','CASCADE');
        //$this->addForeignKey('fk-raport_regulatory-boundary_guid',"{{%raport_regulatory}}",'boundary_guid',"{{%boundary}}",'guid','CASCADE','CASCADE');
        // $this->addForeignKey('fk-raport_regulatory-project_guid',"{{%raport_regulatory}}",'project_guid',"{{%project}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_regulatory-master_guid',"{{%raport_regulatory}}",'master_guid',"{{%user}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_regulatory-user_guid',"{{%raport_regulatory}}",'user_guid',"{{%user}}",'guid','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        // $this->dropForeignKey('fk-raport_regulatory-brigade_guid',"{{%raport_regulatory}}");
        // $this->dropForeignKey('fk-raport_regulatory-object_guid',"{{%raport_regulatory}}");
        // $this->dropForeignKey('fk-raport_regulatory-boundary_guid',"{{%raport_regulatory}}");
        // $this->dropForeignKey('fk-raport_regulatory-project_guid',"{{%raport_regulatory}}");
        
        $this->dropForeignKey('fk-raport_regulatory-user_guid',"{{%raport_regulatory}}");
        $this->dropForeignKey('fk-raport_regulatory-master_guid',"{{%raport_regulatory}}");
        $this->dropForeignKey('fk-raport_regulatory-version_id',"{{%raport_regulatory}}");
        $this->dropForeignKey('fk-raport_regulatory_history-creator_id',"{{%raport_regulatory_history}}");
        $this->dropForeignKey('fk-raport_regulatory_history-entity_id',"{{%raport_regulatory_history}}");

        $this->dropTable('{{%raport_regulatory_history}}');
        $this->dropTable('{{%raport_regulatory}}');
    }

}
