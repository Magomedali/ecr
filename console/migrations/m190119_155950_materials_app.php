<?php

use yii\db\Migration;

/**
 * Class m190119_155950_materials_app
 */
class m190119_155950_materials_app extends Migration
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


        $this->createTable('{{%materials_app}}', [
            'id' => $this->primaryKey(),

            'guid' => $this->string(36)->null()->unique(),
            'created_at'=>$this->timestamp(),
            'number'=>$this->string()->null(),

            'status'=>$this->integer()->null(),
            'user_guid'=>$this->string(36)->notNull(),
            'master_guid'=>$this->string(36)->notNull(),
            'stockroom_guid' => $this->string(36)->notNull(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%materials_app_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'guid' => $this->string(36)->null(),
            'created_at'=>$this->timestamp()->null(),
            'number'=>$this->string()->null(),
            'status'=>$this->integer()->null(),
            'user_guid'=>$this->string(36)->notNull(),
            'master_guid'=>$this->string(36)->notNull(),
            'stockroom_guid' => $this->string(36)->notNull(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-materials_app_history-entity_id',"{{%materials_app_history}}",'entity_id',"{{%materials_app}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-materials_app_history-creator_id',"{{%materials_app_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-materials_app-version_id',"{{%materials_app}}",'version_id',"{{%materials_app_history}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-materials_app-stockroom_guid',"{{%materials_app}}",'stockroom_guid',"{{%stockroom}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-materials_app-user_guid',"{{%materials_app}}",'user_guid',"{{%user}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-materials_app-master_guid',"{{%materials_app}}",'master_guid',"{{%user}}",'guid','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropForeignKey('fk-materials_app-master_guid',"{{%materials_app}}");
        $this->dropForeignKey('fk-materials_app-user_guid',"{{%materials_app}}");
        $this->dropForeignKey('fk-materials_app-stockroom_guid',"{{%materials_app}}");
        $this->dropForeignKey('fk-materials_app-version_id',"{{%materials_app}}");
        $this->dropForeignKey('fk-materials_app_history-creator_id',"{{%materials_app_history}}");
        $this->dropForeignKey('fk-materials_app_history-entity_id',"{{%materials_app_history}}");

        $this->dropTable('{{%materials_app_history}}');
        $this->dropTable('{{%materials_app}}');
    }

}
