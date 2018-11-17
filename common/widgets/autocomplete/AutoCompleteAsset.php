<?php

namespace common\widgets\autocomplete;

use yii\web\AssetBundle;

class AutoCompleteAsset extends AssetBundle{

	public $sourcePath = '@common/widgets/autocomplete/assets';
	
	public $css = [
        'css/autocomplete.css',
    ];

    

	public $js = [
		"js/autocomplete.js"
	];

	public $depends = [
        'yii\web\JqueryAsset',
    ];

}

?>