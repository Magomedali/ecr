<?php

use yii\db\Migration;

/**
 * Class m181105_120933_remnant
 */
class m181105_120933_remnant extends Migration
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


        $this->createTable('{{%remnants_package}}', [
            'id' => $this->primaryKey(),
            'user_guid' => $this->string(36)->notNull(),
            'updated_at'=>$this->timestamp(),
            'isActual'=>$this->smallInteger()->null()->defaultValue(1)
        ], $tableOptions);


        $this->createTable('{{%remnants_item}}', [
            'id' => $this->primaryKey(),
            'package_id' => $this->integer()->notNull(),
            'nomenclature_guid'=>$this->string(36)->notNull(),
            'count'=>$this->float()->notNull()->defaultValue(0),
            'UNIQUE unq_key_package_nomenclature (package_id,nomenclature_guid)'
        ], $tableOptions);


        $this->addForeignKey('fk-remnants_package-user_guid',"{{%remnants_package}}",'user_guid',"{{%user}}",'guid','CASCADE','CASCADE');

        $this->addForeignKey('fk-remnants_item-package_id',"{{%remnants_item}}",'package_id',"{{%remnants_package}}",'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-remnants_item-nomenclature_guid',"{{%remnants_item}}",'nomenclature_guid',"{{%nomenclature}}",'guid','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-remnants_item-package_id',"{{%remnants_item}}");
        $this->dropForeignKey('fk-remnants_item-nomenclature_guid',"{{%remnants_item}}");
        $this->dropForeignKey('fk-remnants_package-user_guid',"{{%remnants_package}}");

        
        $this->dropTable('{{%remnants_item}}');
        $this->dropTable('{{%remnants_package}}');
    }
}
