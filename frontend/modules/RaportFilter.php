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
    public $date = 0;

    public $page_size = 5;

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            [['brigade_guid'],'required']
        ];
    }

    public function scenarios(){
        return Raport::scenarios();
    }


    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function filter($params)
    {   
        $query = RaportFilter::find();


        /**
        * Создаём DataProvider, указываем ему запрос, настраиваем пагинацию
        */
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => new Pagination([
                'pageSize' => $this->page_size
            ])
        ]);


        //если данные не фильтра не переданы или переданы не валидные данныеы
        if(!($this->load($params) && $this->validate())){
            
            
            if($this->brigade_guid){
                $query->where("id < 0");
            }else{
                 $query->where(['brigade_guid'=>$this->brigade_guid]);
            }
            
            return $dataProvider;
        }
        
        
        $query->andWhere(['brigade_guid'=>$this->brigade_guid]);
        

        
        return $dataProvider;
    }

}