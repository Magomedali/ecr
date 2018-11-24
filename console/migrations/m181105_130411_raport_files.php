<?php

use yii\db\Migration;

/**
 * Class m181105_130411_raport_files
 */
class m181105_130411_raport_files extends Migration
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


        $this->createTable('{{%raport_files}}', [
            'id' => $this->primaryKey(),

            'raport_id' => $this->integer()->notNull(),
            'created_at'=>$this->timestamp(),
            'file_name'=>$this->string(255)->notNull(),
            'file_type'=>$this->string(255)->notNull(),
            'file'=>$this->string(255)->notNull(),
            'file_binary'=>$this->binary()->null(),
            'creator_id'=>$this->integer()->null()
        ], $tableOptions);
        
        $this->addForeignKey('fk-raport_files-raport_id',"{{%raport_files}}",'raport_id',"{{%raport}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_files-creator_id',"{{%raport_files}}",'creator_id',"{{%user}}",'id','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-raport_files-raport_id',"{{%raport_files}}");
        $this->dropForeignKey('fk-raport_files-creator_id',"{{%raport_files}}");

        $this->dropTable('{{%raport_files}}');
    }
}
