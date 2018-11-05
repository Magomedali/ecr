<?php

use yii\db\Migration;

/**
 * Class m181105_130913_raport_brigade
 */
class m181105_130913_raport_brigade extends Migration
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


        $this->createTable('{{%raport_brigade}}', [
            'id' => $this->primaryKey(),
            'raport_id' => $this->integer()->notNull(),
            'user_guid'=>$this->string(32)->notNull()
        ], $tableOptions);
        
        $this->addForeignKey('fk-raport_brigade-raport_id',"{{%raport_brigade}}",'raport_id',"{{%raport}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-raport_brigade-user_guid',"{{%raport_brigade}}",'user_guid',"{{%user}}",'guid','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-raport_brigade-raport_id',"{{%raport_brigade}}");
        $this->dropForeignKey('fk-raport_brigade-user_guid',"{{%raport_brigade}}");

        $this->dropTable('{{%raport_brigade}}');
    }
}
