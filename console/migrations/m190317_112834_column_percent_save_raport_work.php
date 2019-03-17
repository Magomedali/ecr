<?php

use yii\db\Migration;

/**
 * Class m190118_082120_column_unit_nomenclature
 */
class m190317_112834_column_percent_save_raport_work extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        
        $this->addColumn("{{%raport_works}}",'percent_save',$this->float()->notNull()->defaultValue(0));
        $this->addColumn("{{%raport_works_history}}",'percent_save',$this->float()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%raport_works}}",'percent_save');
        $this->dropColumn("{{%raport_works_history}}",'percent_save');
    }
}
