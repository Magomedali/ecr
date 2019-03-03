<?php

use yii\db\Migration;

/**
 * Class m190303_112034_raport_consist_technic_nullable
 */
class m190303_112034_raport_consist_technic_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("{{%raport_consist}}","technic_guid",$this->string(36)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn("{{%raport_consist}}","technic_guid",$this->string(36)->notNull());
    }
}
