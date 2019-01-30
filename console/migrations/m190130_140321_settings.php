<?php

use yii\db\Migration;

/**
 * Class m190130_140321_settings
 */
class m190130_140321_settings extends Migration
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


        $this->createTable('{{%setting}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->defaultValue(0),
            'shift_start_hours'=>$this->time()->null(),
            'is_actual'=>$this->smallInteger()->null()->defaultValue(1)
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropTable('{{%setting}}');
    }

   
}
