<?php

namespace frontend\modules;

use yii\data\Pagination;
use yii\db\Query;
use common\models\Raport;
use yii\data\ActiveDataProvider;

class RaportFilter extends Raport
{
    /**
     * Принимаемые моделью входящие данные
     */
    public $month;

    public $page_size = 5;

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            [['brigade_guid','user_guid','master_guid'],'string','length'=>36],
            ['month','safe']
        ];
    }

    public function scenarios(){
        return Raport::scenarios();
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
            '02'=>'Февраль',
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
        $query = RaportFilter::find()
                ->orderby(['created_at'=>SORT_DESC]);


        /**
        * Создаём DataProvider, указываем ему запрос, настраиваем пагинацию
        */
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>[
                'pageSize'=>$this->page_size
            ]
        ]);


        //если данные не фильтра не переданы или переданы не валидные данныеы
        if(!($this->load($params) && $this->validate())){
            $query->where("id < 0");
        
            return $dataProvider;
        }
        
        
        if($this->brigade_guid){
            $query->andWhere(['brigade_guid'=>$this->brigade_guid]);
        }

        if($this->user_guid){
           $query->andWhere(['user_guid'=>$this->user_guid]); 
        }

        if($this->master_guid){
           $query->andWhere(['master_guid'=>$this->master_guid]); 
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

            $query->andFilterWhere(['>=', 'created_at', date("Y.m.d",strtotime($start))]);
            $query->andFilterWhere(['<=', 'created_at', date("Y.m.d",strtotime($end))]);
            
            $dataProvider->getPagination()->setPageSize(null);
        }

        
        return $dataProvider;
    }

}