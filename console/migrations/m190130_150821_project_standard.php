<?php

use yii\db\Migration;

/**
 * Class m190130_150821_project_standard
 */
class m190130_150821_project_standard extends Migration
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
        
        $this->createTable('{{%project_standard}}', [
            'id' => $this->primaryKey(),
            'project_guid' => $this->string(36)->notNull(),
            'typeofwork_guid' => $this->string(36)->notNull(),
            'standard' => $this->float()->notNull()->defaultValue(0),
            "UNIQUE un_project_standard (project_guid,typeofwork_guid)"
        ], $tableOptions);

        $this->addForeignKey('fk-project_standard-typeofwork_guid',"{{%project_standard}}",'typeofwork_guid',"{{%typeofwork}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-project_standard-project_guid',"{{%project_standard}}",'project_guid',"{{%project}}",'guid','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-project_standard-project_guid',"{{%project_standard}}");
        $this->dropForeignKey('fk-project_standard-typeofwork_guid',"{{%project_standard}}");

        $this->dropTable('{{%project_standard}}'); 
    }
}
