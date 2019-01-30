<?php

use yii\db\Migration;

/**
 * Class m190130_145830_rel_typeofwork_and_nomenclature
 */
class m190130_145830_rel_typeofwork_and_nomenclature extends Migration
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

        $this->createTable('{{%rel_typeofwork_nomenclature}}', [
            'typeofwork_guid' => $this->string(36)->notNull(),
            'nomenclature_guid' => $this->string(36)->notNull(),
            "UNIQUE un_typeofwork_nomenclature (typeofwork_guid,nomenclature_guid)"
        ], $tableOptions);

        $this->addForeignKey('fk-rel_typeofwork_nomenclature-typeofwork_guid',"{{%rel_typeofwork_nomenclature}}",'typeofwork_guid',"{{%typeofwork}}",'guid','CASCADE','CASCADE');
        $this->addForeignKey('fk-rel_typeofwork_nomenclature-nomenclature_guid',"{{%rel_typeofwork_nomenclature}}",'nomenclature_guid',"{{%nomenclature}}",'guid','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-rel_typeofwork_nomenclature-nomenclature_guid',"{{%rel_typeofwork_nomenclature}}");
        $this->dropForeignKey('fk-rel_typeofwork_nomenclature-typeofwork_guid',"{{%rel_typeofwork_nomenclature}}");

        $this->dropTable('{{%rel_typeofwork_nomenclature}}'); 
    }

}
