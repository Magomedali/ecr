<?php

namespace frontend\modules;

use yii\data\Pagination;
use yii\db\Query;
use yii\data\ArrayDataProvider;
use yii\base\Model;
use common\modules\ImportTotalOutput;

class TotalOutputFilter extends Model
{
    /**
     * Принимаемые моделью входящие данные
     */
    public $brigade_guid;
    public $month;

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            [['brigade_guid'],'required'],
            ['month','safe']
        ];
    }



    public function load($data, $formName = null)
    {
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data)) {
            $this->setAttributes($data);

            return true;
        } elseif (isset($data[$scope])) {
            $this->setAttributes($data[$scope]);

            return true;
        }

        return false;
    }


    public static function getMonths(){
        return [
            '01'=>'Январь',
            '02'=>'Февряль',
            '03'=>'Март',
            '04'=>'Апрель',
            '05'=>'Май',
            '06'=>'Июнь',
            '07'=>'Июль',
            '08'=>'Август',
            '09'=>'Сентябрь',
            '10'=>'Октябрь',
            '11'=>'Ноябрь',
            '12'=>'Декабрь',
        ];
    }

    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function filter($params)
    {   
        $outputs = [];

        $dataProvider = new ArrayDataProvider([
            'allModels' => $outputs
        ]);


        //если данные не фильтра не переданы или переданы не валидные данныеы
        if(!($this->load($params) && $this->validate())){
            
            return $dataProvider;
        }
        
        
        

        if($this->month){
            $now = time();
            $Y = date("Y");
            $m = $this->month;
            $month_start = strtotime(date("{$Y}-{$m}-01"));
            $Y = $month_start > $now ? --$Y : $Y;
            
            $start = date("{$Y}-{$m}-01");

            if((int)$m == 12){
                ++$Y;
                $m = '01';
            }else{
                $m = (int)$m+1;
            }

            $end = date("{$Y}-{$m}-01");
            $outputs = ImportTotalOutput::import($this->brigade_guid,date("Y-m-d",strtotime($start)));
        }else{
            $outputs = ImportTotalOutput::import($this->brigade_guid,date("Y-m-d"));
        }

        // echo "<PRE>";
        // print_r($outputs);
        // echo "</PRE>";
        // exit;
        $dataProvider->allModels = $outputs; 
        return $dataProvider;
    }

}