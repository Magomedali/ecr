<?php

use yii\db\Migration;

/**
 * Class m190118_082120_column_unit_nomenclature
 */
class m190118_082120_column_unit_nomenclature extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->addColumn("{{%nomenclature}}",'unit',$this->string(255)->null());
        $this->addColumn("{{%nomenclature_history}}",'unit',$this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%nomenclature_history}}",'unit');
        $this->dropColumn("{{%nomenclature}}",'unit');
    }
}
