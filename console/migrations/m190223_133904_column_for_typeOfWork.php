<?php

use yii\db\Migration;

/**
 * Class m190223_133904_column_for_typeOfWork
 */
class m190223_133904_column_for_typeOfWork extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%typeofwork}}",'is_regulatory',$this->smallInteger()->null()->defaultValue(0));
        $this->addColumn("{{%typeofwork_history}}",'is_regulatory',$this->smallInteger()->null()->defaultValue(0));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%typeofwork_history}}",'is_regulatory');
        $this->dropColumn("{{%typeofwork}}",'is_regulatory');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190223_133904_column_for_typeOfWork cannot be reverted.\n";

        return false;
    }
    */
}
