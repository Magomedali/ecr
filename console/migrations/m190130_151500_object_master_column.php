<?php

use yii\db\Migration;

/**
 * Class m190130_151500_object_master_column
 */
class m190130_151500_object_master_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%object}}",'master_guid',$this->string(36)->null());
        $this->addColumn("{{%object_history}}",'master_guid',$this->string(36)->null());

        $this->addForeignKey("FK-object-masterg_guid","{{%object}}","master_guid","{{%user}}","guid","SET NULL","CASCADE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropForeignKey("FK-object-masterg_guid","{{%object}}");
        $this->dropColumn("{{%object_history}}",'master_guid');
        $this->dropColumn("{{%object}}",'master_guid');
    }

    
}
