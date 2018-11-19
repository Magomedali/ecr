<?php

use yii\db\Migration;

/**
 * Class m181105_131644_raport_consist
 */
class m181105_131644_raport_consist extends Migration
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


        $this->createTable('{{%raport_consist}}', [
            'id' => $this->primaryKey(),
            'raport_id' => $this->integer()->notNull(),
            'technic_guid'=>$this->string(36)->notNull(),
            'user_guid'=>$this->string(36)->notNull()
        ], $tableOptions);
        
        $this->addForeignKey('fk-raport_consist-raport_id',"{{%raport_consist}}",'raport_id',"{{%raport}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_consist-technic_guid',"{{%raport_consist}}",'technic_guid',"{{%technic}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_consist-user_guid',"{{%raport_consist}}",'user_guid',"{{%user}}",'guid','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-raport_consist-raport_id',"{{%raport_consist}}");
        $this->dropForeignKey('fk-raport_consist-user_guid',"{{%raport_consist}}");
        $this->dropForeignKey('fk-raport_consist-technic_guid',"{{%raport_consist}}");

        $this->dropTable('{{%raport_consist}}');
    }
}
