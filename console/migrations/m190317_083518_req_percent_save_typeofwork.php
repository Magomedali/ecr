<?php

use yii\db\Migration;

/**
 * Class m190317_083518_req_percent_save_typeofwork
 */
class m190317_083518_req_percent_save_typeofwork extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        
        $this->addColumn("{{%typeofwork}}",'req_percent_save',$this->smallInteger()->null()->defaultValue(0));
        $this->addColumn("{{%typeofwork_history}}",'req_percent_save',$this->smallInteger()->null()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%typeofwork}}",'req_percent_save');
        $this->dropColumn("{{%typeofwork_history}}",'req_percent_save');
    }
}
