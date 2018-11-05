<?php

use yii\db\Migration;

/**
 * Class m181105_123340_raport
 */
class m181105_123340_raport extends Migration
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


        $this->createTable('{{%raport}}', [
            'id' => $this->primaryKey(),

            'guid' => $this->string(32)->notNull()->unique(),
            'number'=>$this->string()->null(),
            'status'=>$this->integer()->null(),
            'created_at'=>$this->timestamp(),
            'starttime'=>$this->timestamp()->null(),
            'endtime'=>$this->timestamp()->null(),

            'temperature_start'=>$this->float()->null(),
            'temperature_end'=>$this->float()->null(),
            'surface_temperature_start'=>$this->float()->null(),
            'surface_temperature_end'=>$this->float()->null(),
            'airhumidity_start'=>$this->float()->null(),
            'airhumidity_end'=>$this->float()->null(),

            'brigade_guid' => $this->string(32)->notNull(),
            'object_guid' => $this->string(32)->notNull(),
            'boundary_guid' => $this->string(32)->notNull(),
            'project_guid' => $this->string(32)->notNull(),

            'comment' => $this->text()->null(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%raport_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'guid' => $this->string(32)->notNull(),
            'number'=>$this->string()->null(),
            'status'=>$this->integer()->null(),
            'created_at'=>$this->timestamp(),
            'starttime'=>$this->timestamp()->null(),
            'endtime'=>$this->timestamp()->null(),

            'temperature_start'=>$this->float()->null(),
            'temperature_end'=>$this->float()->null(),
            'surface_temperature_start'=>$this->float()->null(),
            'surface_temperature_end'=>$this->float()->null(),
            'airhumidity_start'=>$this->float()->null(),
            'airhumidity_end'=>$this->float()->null(),

            'brigade_guid' => $this->string(32)->notNull(),
            'object_guid' => $this->string(32)->notNull(),
            'boundary_guid' => $this->string(32)->notNull(),
            'project_guid' => $this->string(32)->notNull(),

            'comment' => $this->text()->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-raport_history-entity_id',"{{%raport_history}}",'entity_id',"{{%raport}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_history-creator_id',"{{%raport_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-raport-version_id',"{{%raport}}",'version_id',"{{%raport_history}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-raport-brigade_guid',"{{%raport}}",'brigade_guid',"{{%brigade}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport-object_guid',"{{%raport}}",'object_guid',"{{%object}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport-boundary_guid',"{{%raport}}",'boundary_guid',"{{%boundary}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport-project_guid',"{{%raport}}",'project_guid',"{{%project}}",'guid','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-raport-brigade_guid',"{{%raport}}");
        $this->dropForeignKey('fk-raport-object_guid',"{{%raport}}");
        $this->dropForeignKey('fk-raport-boundary_guid',"{{%raport}}");
        $this->dropForeignKey('fk-raport-project_guid',"{{%raport}}");
        $this->dropForeignKey('fk-raport-version_id',"{{%raport}}");
        $this->dropForeignKey('fk-raport_history-creator_id',"{{%raport_history}}");
        $this->dropForeignKey('fk-raport_history-entity_id',"{{%raport_history}}");

        $this->dropTable('{{%raport_history}}');
        $this->dropTable('{{%raport}}');
    }
}
