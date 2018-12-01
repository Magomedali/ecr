<?php

namespace backend\modules;

use yii\db\Query;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use common\models\User;

class UserSearch extends User{
	 

    public $page_size = 30;

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            // Только числа, значение как минимум должна равняться единице
            [['login','name'],'safe'],

        ];
    }

    public function scenarios(){
        return User::scenarios();
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

    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function search($params)
    {   
        $query = User::find()
        				->where("`guid` IS NOT NULL and `login` IS NOT NULL")
        				->andWhere(['is_master'=>0]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
            'pagination' => new Pagination([
                    'pageSize' => $this->page_size
                ])
        ]);


        if(!($this->load($params) && $this->validate())){

            return $dataProvider;
        }

        if($this->login){
        	$query->andFilterWhere(['like','login',$this->login]);
        }
        if($this->name){
        	$query->andFilterWhere(['like','name',$this->name]);
        }

        return $dataProvider;
    }
}
?>