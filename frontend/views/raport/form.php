<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\widgets\raportform\RaportForm;
use common\dictionaries\ExchangeStatuses;

$user = Yii::$app->user->identity;
$masters = User::find()->where(['is_master'=>true])->asArray()->all();

if(!$hasErrors){
	
	$BrigadeConsist = !isset($model->id) ? $user->brigadeConsist : $model->consist;
	$ActualBrigadeRemnants = !isset($model->id) ? $user->actualBrigadeRemnants : $model->materials;
	
	if(isset($model->id)){
		if($model->isWrongMaterials($ActualBrigadeRemnants)){
			Yii::$app->session->setFlash("warning","В остатках недостаточное количество материалов!");
		}
	}

	$RaportWorks =isset($model->id) ? $model->works : [[
		'work_guid'=>null,
		'work_name'=>null,
		'work_nomenclatures'=>null,
		'line_guid'=>null,
		'line_name'=>null,
		'mechanized'=>null,
		'length'=>null,
		'hint_length'=>null,
		'count'=>null,
		'hint_count'=>null,
		'squaremeter'=>null
	]];
}else{
	$BrigadeConsist = $errorsRaportConsist;
	$ActualBrigadeRemnants = $errorsRaportMaterials;
	$RaportWorks = $errorsRaportWorks;
}



if(isset($model->id)){
	$object = $model->object_guid ? $model->object : null;
	$object_name = isset($object->id) ? $object->name : "";

	$boundary = isset($object->id) && $object->boundary_guid ? $object->boundary : null;
	$boundary_name = isset($boundary->id) ? $boundary->name : null;

	$project = isset($model->project_guid) ? $model->project : null;
	$project_name = isset($project->id) ? $project->name : null;

	$master = isset($model->master_guid) ? $model->master : null;
	$master_name = isset($master->id) ? $master->name : null;
}else{
	$boundary = $object = $project = null;
	if($hasErrors){
		$boundary_name = $errorsRaport['boundary_name'];
		$project_name = $errorsRaport['project_name'];
		$object_name = $errorsRaport['object_name'];
		$master_name = $errorsRaport['master_name'];
	}else{
		$boundary_name = $project_name = $object_name = $master_name = "";
	}
}


$this->title = "Форма рапорта";
$this->params['backlink']['url']=Url::to(['raport/index']);

$loadStandarsUrl = Url::to(['autocomplete/project-standarts']);
?>

<?php 
	
	$formParams = [
		'model'=>$model,
		'user'=>$user,
		'masters'=>$masters,
		'BrigadeConsist'=>$BrigadeConsist,
		'ActualBrigadeRemnants'=>$ActualBrigadeRemnants,
		'RaportWorks'=>$RaportWorks,
		'object_name'=>$object_name,
		'boundary_name'=>$boundary_name,
		'project_name'=>$project_name,
		'master_name'=>$master_name,
		'loadStandarsUrl'=>$loadStandarsUrl,
		'inValidPassword'=>$inValidPassword,
		'requiredFile'=>true,
		'enableGuardPassword'=>true,
		'urlLoadRaportWorkRow'=>['raport/get-row-work'],
		'urlLoadRaportConsistRow'=>['raport/get-row-consist']
	];

	if($user->is_master){
		$formParams['enableGuardPassword'] = false;
		$formParams['updateStatus'] = true;
		$formParams['statuses'] = ExchangeStatuses::getLabels();
		$formParams['forManager']=true;
		$formParams['disableMaster']=true;
		
	}
	

	echo RaportForm::widget($formParams);

?>
