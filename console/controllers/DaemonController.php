<?php
namespace console\controllers;
 
use Yii;
use yii\helpers\Url;
use yii\console\Controller;
use common\models\Request;
use console\models\RequestMethodFactory;

/**
 * Daemon controller
 */
class DaemonController extends Controller {
 
    public function actionIndex() {
        echo "Yes, cron service is running.";
        Yii::info("Yes, cron service is running.",'cron');
    }
  


    
    public function actionResend() {
      // called every two minutes
      // */2 * * * * ~/sites/www/yii2/yii test
      Yii::info("Process start",'cron');
      $time_start = microtime(true);

      $errorRequests = Request::find()->where(['result'=>0,'completed'=>0])->all();
      try {
        foreach ($errorRequests as $r) {

          $reqRepeater = RequestMethodFactory::make($r);
          if(!$reqRepeater){
            Yii::info('handler for request not founded','cron');
            echo "handler for request not founded\n";
            continue;
          }
          $reqRepeater->repeat();
        }
      } catch (\Exception $e) {
          Yii::info($e->getMessage(),'cron');
          echo $e->getMessage(),"\n";
      }
      
      $time_end = microtime(true);
      Yii::info("Processing for ".($time_end-$time_start)." seconds",'cron');
      echo "Processing for ".($time_end-$time_start)." seconds \n ";
    }
}