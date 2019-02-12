<?php

namespace common\widgets\raportform;

use yii\web\AssetBundle;

class RaportFormAsset extends AssetBundle{

	public $sourcePath = '@common/widgets/raportform/assets';
	
	public $css = [
        'css/style.css',
    ];

    
	public $js = [
		"js/script.js"
	];

	public $depends = [
        'yii\web\JqueryAsset',
    ];

}

?>