<?php

namespace common\widgets\reqpasspresubmit;

use yii\web\AssetBundle;

class ReqPassPreSubmitAsset extends AssetBundle{

	public $sourcePath = '@common/widgets/reqpasspresubmit/assets';
	
	public $css = [
        'css/modal.css',
    ];

    
	public $js = [
		"js/modal.js"
	];

	public $depends = [
        'yii\web\JqueryAsset',
    ];

}

?>