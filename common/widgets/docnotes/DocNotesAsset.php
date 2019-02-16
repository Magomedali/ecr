<?php

namespace common\widgets\docnotes;

use yii\web\AssetBundle;

class DocNotesAsset extends AssetBundle{

	public $sourcePath = '@common/widgets/docnotes/assets';
	
	public $css = [
        'css/docnotes.css',
    ];

    
	public $js = [
		//"js/script.js"
	];

	public $depends = [
        'yii\web\JqueryAsset',
    ];

}

?>