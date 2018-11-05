<?php

use yii\db\Migration;

/**
 * Class m181105_103630_objects_boundary_project
 */
class m181105_103630_objects_boundary_project extends Migration
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


        $this->createTable('{{%boundary}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(128)->notNull(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%boundary_history}}', [
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

        
        $this->addForeignKey('fk-boundary_history-entity_id',"{{%boundary_history}}",'entity_id',"{{%boundary}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-boundary_history-creator_id',"{{%boundary_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-boundary-version_id',"{{%boundary}}",'version_id',"{{%boundary_history}}",'id','CASCADE','CASCADE');






        $this->createTable('{{%object}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(128)->notNull(),
            'boundary_id' => $this->integer()->null(),
            'boundary_guid' => $this->string(32)->null(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%object_history}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),

            'guid' => $this->string(32)->notNull(),
            'name' => $this->string(128)->notNull(),
            'boundary_id' => $this->integer()->null(),
            'boundary_guid' => $this->string(32)->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);

        
        $this->addForeignKey('fk-object_history-entity_id',"{{%object_history}}",'entity_id',"{{%object}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-object_history-creator_id',"{{%object_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-object-version_id',"{{%object}}",'version_id',"{{%object_history}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-object-boundary_id',"{{%object}}",'boundary_id',"{{%boundary}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-object-boundary_guid',"{{%object}}",'boundary_guid',"{{%boundary}}",'guid','CASCADE','CASCADE');
    




        $this->createTable('{{%project}}', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(128)->notNull(),

            'version_id'=>$this->integer()->null(),
            'isDeleted'=>$this->smallInteger()->null()->defaultValue(0)
        ], $tableOptions);


        $this->createTable('{{%project_history}}', [
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

        
        $this->addForeignKey('fk-project_history-entity_id',"{{%project_history}}",'entity_id',"{{%project}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-project_history-creator_id',"{{%project_history}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-project-version_id',"{{%project}}",'version_id',"{{%project_history}}",'id','CASCADE','CASCADE');


        $this->createTable('{{%rel_project_object}}', [
            'project_guid' => $this->string(32)->notNull(),
            'object_guid' => $this->string(32)->notNull(),
            "UNIQUE un_project_object (project_guid,object_guid)"
        ], $tableOptions);

        $this->addForeignKey('fk-rel_project_object-project_guid',"{{%rel_project_object}}",'project_guid',"{{%project}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-rel_project_object-object_guid',"{{%rel_project_object}}",'object_guid',"{{%object}}",'guid','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-rel_project_object-project_guid',"{{%rel_project_object}}");
        $this->dropForeignKey('fk-rel_project_object-object_guid',"{{%rel_project_object}}");
        $this->dropTable('{{%rel_project_object}}');
        
        $this->dropForeignKey('fk-project-version_id',"{{%project}}");
        $this->dropForeignKey('fk-project_history-creator_id',"{{%project_history}}");
        $this->dropForeignKey('fk-project_history-entity_id',"{{%project_history}}");

        $this->dropTable('{{%project_history}}');
        $this->dropTable('{{%project}}');


        $this->dropForeignKey('fk-object-version_id',"{{%object}}");
        $this->dropForeignKey('fk-object-boundary_id',"{{%object}}");
        $this->dropForeignKey('fk-object-boundary_guid',"{{%object}}");
        $this->dropForeignKey('fk-object_history-creator_id',"{{%object_history}}");
        $this->dropForeignKey('fk-object_history-entity_id',"{{%object_history}}");

        $this->dropTable('{{%object_history}}');
        $this->dropTable('{{%object}}');        


        $this->dropForeignKey('fk-boundary-version_id',"{{%boundary}}");
        $this->dropForeignKey('fk-boundary_history-creator_id',"{{%boundary_history}}");
        $this->dropForeignKey('fk-boundary_history-entity_id',"{{%boundary_history}}");

        $this->dropTable('{{%boundary_history}}');
        $this->dropTable('{{%boundary}}');
    }

}
