<?php
namespace console\controllers;
 
use Yii;
use yii\helpers\Url;
use yii\console\Controller;
 
/**
 * Daemon controller
 */
class DaemonController extends Controller {
 
    public function actionIndex() {
        echo "Yes, cron service is running.";
    }
 
    public function actionFrequent() {
      // called every two minutes
      // */2 * * * * ~/sites/www/yii2/yii test
      Yii::info("Process start",'cron');
      $time_start = microtime(true);

      

      $time_end = microtime(true);

      Yii::info('Processing for '.($time_end-$time_start).' seconds','cron');
      echo 'Processing for '.($time_end-$time_start).' seconds';
    }
}