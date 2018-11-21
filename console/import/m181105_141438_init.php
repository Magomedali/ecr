<?php

use yii\db\Migration;

use yii\base\InvalidConfigException;
use yii\rbac\DbManager;
/**
 * Class m181105_141438_init
 */
class m181105_141438_init extends Migration
{

    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        return $authManager;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert("{{%user}}",[
            'guid'=>null,
            'name'=>'Администратор',
            'login'=>'admin',
            'auth_key'=>\Yii::$app->security->generateRandomString(),
            'password_hash'=>\Yii::$app->security->generatePasswordHash("1Q2w3e4r")
        ]);

        $this->insert("{{%user}}",[
            'guid'=>null,
            'name'=>'Суперадмин',
            'login'=>'superadmin',
            'auth_key'=>\Yii::$app->security->generateRandomString(),
            'password_hash'=>\Yii::$app->security->generatePasswordHash("12345qwE")
        ]);

        $authManager = $this->getAuthManager();

        $this->insert($authManager->itemTable,[
                'name'=>'superadmin',
                'type'=>1,
        ]);

        $this->insert($authManager->itemTable,[
                'name'=>'administrator',
                'type'=>1,
        ]);
        $admin_id = \Yii::$app->db->createCommand("SELECT id FROM {{%user}} WHERE login='admin' LIMIT 1")->queryScalar();

        $superadmin_id = \Yii::$app->db->createCommand("SELECT id FROM {{%user}} WHERE login='superadmin' LIMIT 1")->queryScalar();
        
        if($admin_id){
            $this->insert($authManager->assignmentTable,[
                'item_name'=>'administrator',
                'user_id'=>$admin_id,
            ]);
        }

        if($superadmin_id){
            $this->insert($authManager->assignmentTable,[
                'item_name'=>'administrator',
                'user_id'=>$superadmin_id,
            ]);
            $this->insert($authManager->assignmentTable,[
                'item_name'=>'superadmin',
                'user_id'=>$superadmin_id,
            ]);
        }
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $authManager = $this->getAuthManager();
        $this->delete($authManager->assignmentTable);
        $this->delete($authManager->itemTable);
        $this->delete("{{%user}}");
    }

}
