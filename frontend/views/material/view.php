<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\widgets\autocomplete\AutoComplete;
use common\models\RaportWork;
use common\models\RaportFile;

$user = Yii::$app->user->identity;

$stockroom = $model->stockroom_guid ? $model->stockroom : null;
$stockroom_name = isset($stockroom->id) ? $stockroom->name : "";


$MaterialsAppItems = $model->getMaterialsAppItems();



$this->title = "Заявка " . $model->number;

?>
<div class="row">
	<div class="col-md-12">
		
<?php
	echo "<PRE>";
	print_r($model->attributes);
	print_r($MaterialsAppItems);
	echo "</PRE>";
?>
	</div>
</div>

